<?php

namespace App\Services;

use App\Models\Category;
use App\Models\DailyLog;
use App\Models\Step;
use App\Models\FocusSession;
use Illuminate\Support\Carbon;

class MissionService
{
    /**
     * Get today's mission based on energy mode and task state.
     */
    public function getMission(?string $energyMode = null): ?array
    {
        $energyMode = $energyMode ?? $this->getTodayEnergyMode() ?? 'normal';

        if ($energyMode === 'low') {
            return $this->getMinimumViableMission();
        }

        $step = $this->selectBestStep($energyMode);
        if (!$step) return null;

        $category = $step->category;
        $reasons = $this->getReasons($step);
        $xp = $this->estimateXP($step, $energyMode);
        $estimatedMinutes = $this->estimateMinutes($step, $energyMode);

        return [
            'step' => $step,
            'category' => $category,
            'reasons' => $reasons,
            'xp' => $xp,
            'estimated_minutes' => $estimatedMinutes,
            'energy_mode' => $energyMode,
            'is_minimum_day' => false,
        ];
    }

    /**
     * Minimum Viable Day: one small task to maintain momentum.
     */
    public function getMinimumViableMission(): ?array
    {
        // Prefer in_progress steps first, prioritize by category priority
        $step = Step::where('status', 'in_progress')
            ->whereNull('steps.parent_id')
            ->whereHas('category', fn($q) => $q->active())
            ->join('categories', 'steps.category_id', '=', 'categories.id')
            ->orderByRaw("CASE categories.priority WHEN 'critical' THEN 1 WHEN 'high' THEN 2 WHEN 'normal' THEN 3 WHEN 'low' THEN 4 ELSE 5 END")
            ->select('steps.*')
            ->first();

        // Otherwise pick the shortest todo from highest-priority category
        if (!$step) {
            $step = Step::where('status', 'todo')
                ->whereNull('steps.parent_id')
                ->whereHas('category', fn($q) => $q->active())
                ->join('categories', 'steps.category_id', '=', 'categories.id')
                ->orderByRaw("CASE categories.priority WHEN 'critical' THEN 1 WHEN 'high' THEN 2 WHEN 'normal' THEN 3 WHEN 'low' THEN 4 ELSE 5 END")
                ->select('steps.*')
                ->first();
        }

        if (!$step) return null;

        return [
            'step' => $step,
            'category' => $step->category,
            'reasons' => ['Just 15 minutes to maintain momentum.'],
            'xp' => 20,
            'estimated_minutes' => 15,
            'energy_mode' => 'low',
            'is_minimum_day' => true,
        ];
    }

    /**
     * Core selection algorithm: score all available steps, pick the best.
     */
    protected function selectBestStep(string $energyMode): ?Step
    {
        $steps = Step::with('category')
            ->whereIn('status', ['todo', 'in_progress'])
            ->whereNull('parent_id')
            ->whereHas('category', fn($q) => $q->active())
            ->get();

        if ($steps->isEmpty()) return null;

        $scored = $steps->map(fn($step) => [
            'step' => $step,
            'score' => $this->scoreStep($step, $energyMode),
        ])->sortByDesc('score');

        return $scored->first()['step'];
    }

    /**
     * Score a step based on multiple factors.
     */
    protected function scoreStep(Step $step, string $energyMode): float
    {
        $score = 0;

        // Factor 1: Status bonus (in_progress = already started, lower friction)
        if ($step->status === 'in_progress') {
            $score += 30;
        }

        // Factor 2: Overdue urgency
        if ($step->isOverdue()) {
            $daysOverdue = $step->due_date->diffInDays(today());
            $score += min(40, 20 + ($daysOverdue * 5));
        } elseif ($step->isDueSoon()) {
            $score += 15;
        }

        // Factor 3: Staleness (days since last touched)
        $lastTouched = $this->getLastTouchedDate($step);
        if ($lastTouched) {
            $daysSince = $lastTouched->diffInDays(today());
            $score += min(25, $daysSince * 3); // 3 pts per day untouched, max 25
        } else {
            $score += 10; // never touched = slight boost
        }

        // Factor 4: Category priority (critical > high > normal > low)
        $priority = $step->category->priority ?? 'normal';
        $score += match($priority) {
            'critical' => 40,
            'high' => 25,
            'normal' => 10,
            'low' => 0,
        };

        // Factor 4b: Category deadline urgency
        if ($step->category->deadline) {
            $daysUntilDeadline = today()->diffInDays($step->category->deadline, false);
            if ($daysUntilDeadline < 0) {
                $score += 35; // category overdue
            } elseif ($daysUntilDeadline <= 7) {
                $score += 25; // due this week
            } elseif ($daysUntilDeadline <= 14) {
                $score += 15; // due within 2 weeks
            }
        }

        // Factor 4c: Category progress (prefer categories closer to completion)
        $categoryProgress = $step->category->progress ?? 0;
        if ($categoryProgress > 60) {
            $score += 10;
        }

        // Factor 5: Energy matching
        $titleLength = strlen($step->title);
        if ($energyMode === 'peak') {
            // Prefer complex tasks when energy is peak
            if ($titleLength > 30) $score += 10;
        } elseif ($energyMode === 'high') {
            // Normal weighting, slight preference for medium tasks
            $score += 5;
        } else {
            // Normal mode: prefer shorter/simpler tasks
            if ($titleLength < 30) $score += 10;
        }

        // Factor 6: Blocking siblings (if this step's category has steps dependent on its position)
        $laterStepsInCategory = Step::where('category_id', $step->category_id)
            ->where('status', 'todo')
            ->where('sort_order', '>', $step->sort_order ?? 0)
            ->count();
        if ($laterStepsInCategory > 2) {
            $score += min(15, $laterStepsInCategory * 3);
        }

        return $score;
    }

    /**
     * Build "why this?" reasons for display.
     */
    protected function getReasons(Step $step): array
    {
        $reasons = [];

        if ($step->status === 'in_progress') {
            $reasons[] = 'Already started';
        }

        if ($step->isOverdue()) {
            $days = $step->due_date->diffInDays(today());
            $reasons[] = "Overdue by {$days} day" . ($days > 1 ? 's' : '');
        } elseif ($step->isDueSoon()) {
            $reasons[] = 'Due soon';
        }

        $lastTouched = $this->getLastTouchedDate($step);
        if ($lastTouched && $lastTouched->diffInDays(today()) >= 3) {
            $days = (int) $lastTouched->diffInDays(today());
            $reasons[] = "Untouched for {$days} days";
        }

        $laterSteps = Step::where('category_id', $step->category_id)
            ->where('status', 'todo')
            ->where('sort_order', '>', $step->sort_order ?? 0)
            ->count();
        if ($laterSteps >= 2) {
            $reasons[] = "Blocking {$laterSteps} other steps";
        }

        $categoryProgress = $step->category->progress ?? 0;
        if ($categoryProgress > 60) {
            $reasons[] = "Category is {$categoryProgress}% done";
        }

        $priority = $step->category->priority ?? 'normal';
        if (in_array($priority, ['critical', 'high'])) {
            array_unshift($reasons, ucfirst($priority) . ' priority category');
        }

        if (empty($reasons)) {
            $reasons[] = 'Next logical step';
        }

        return $reasons;
    }

    /**
     * Estimate XP for completing this mission.
     */
    protected function estimateXP(Step $step, string $energyMode): int
    {
        $base = 30;

        if ($step->isOverdue()) $base += 10;
        if ($energyMode === 'peak') $base += 10;
        if ($step->category && $step->category->progress > 70) $base += 10;

        return $base;
    }

    /**
     * Estimate time in minutes.
     */
    protected function estimateMinutes(Step $step, string $energyMode): int
    {
        return match($energyMode) {
            'peak' => 60,
            'high' => 45,
            'normal' => 30,
            'low' => 15,
            default => 30,
        };
    }

    /**
     * Get when this step was last interacted with.
     */
    protected function getLastTouchedDate(Step $step): ?Carbon
    {
        // Check focus sessions
        $lastSession = FocusSession::where('step_id', $step->id)
            ->orWhere('category_id', $step->category_id)
            ->orderByDesc('started_at')
            ->first();

        if ($lastSession) {
            return $lastSession->started_at;
        }

        // Fall back to updated_at
        return $step->updated_at?->diffInDays(today()) > 0 ? $step->updated_at : null;
    }

    /**
     * Get today's energy mode from daily log.
     */
    public function getTodayEnergyMode(): ?string
    {
        $log = DailyLog::today();
        return $log?->energy_mode;
    }
}
