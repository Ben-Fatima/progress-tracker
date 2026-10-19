<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\DailyLog;
use App\Models\FocusSession;
use App\Models\Step;
use App\Models\WeeklyGoal;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class WeeklyReview extends Component
{
    public string $weekOffset = '0';

    // Goal setting
    public array $newGoals = ['', '', ''];
    public string $biggestWin = '';
    public string $biggestBlocker = '';
    public string $priorityNextWeek = '';
    public array $prevGoalMet = [];
    public bool $saved = false;

    public function previousWeek(): void
    {
        $this->weekOffset = (string) ((int) $this->weekOffset + 1);
    }

    public function nextWeek(): void
    {
        if ((int) $this->weekOffset > 0) {
            $this->weekOffset = (string) ((int) $this->weekOffset - 1);
        }
    }

    public function saveGoals(): void
    {
        $weekStart = today()->startOfWeek()->subWeeks((int) $this->weekOffset);

        $goals = array_values(array_filter($this->newGoals, fn($g) => trim($g) !== ''));

        $existing = WeeklyGoal::whereDate('week_start', $weekStart->toDateString())->first();

        if ($existing) {
            $existing->update([
                'goals' => $goals,
                'biggest_win' => $this->biggestWin ?: null,
                'biggest_blocker' => $this->biggestBlocker ?: null,
                'priority_next_week' => $this->priorityNextWeek ?: null,
            ]);
        } else {
            WeeklyGoal::create([
                'week_start' => $weekStart->toDateString(),
                'goals' => $goals,
                'biggest_win' => $this->biggestWin ?: null,
                'biggest_blocker' => $this->biggestBlocker ?: null,
                'priority_next_week' => $this->priorityNextWeek ?: null,
            ]);
        }

        $this->saved = true;
    }

    public function markPrevGoalMet(int $index, bool $met): void
    {
        $this->prevGoalMet[$index] = $met;
        $prevWeekStart = today()->startOfWeek()->subWeeks((int) $this->weekOffset + 1);
        $prevGoal = WeeklyGoal::forWeek($prevWeekStart);

        if ($prevGoal) {
            $metArray = $this->prevGoalMet;
            $prevGoal->update(['met' => $metArray]);
        }
    }

    public function render()
    {
        $offset = (int) $this->weekOffset;
        $weekStart = today()->startOfWeek()->subWeeks($offset);
        $weekEnd = $weekStart->copy()->endOfWeek();
        $isCurrentWeek = $offset === 0;

        // Load existing weekly goal
        $weeklyGoal = WeeklyGoal::forWeek($weekStart);
        $prevWeekGoal = WeeklyGoal::forWeek($weekStart->copy()->subWeek());

        if ($weeklyGoal && empty($this->newGoals[0])) {
            $goals = $weeklyGoal->goals ?? [];
            for ($i = 0; $i < 3; $i++) {
                $this->newGoals[$i] = $goals[$i] ?? '';
            }
            $this->biggestWin = $weeklyGoal->biggest_win ?? '';
            $this->biggestBlocker = $weeklyGoal->biggest_blocker ?? '';
            $this->priorityNextWeek = $weeklyGoal->priority_next_week ?? '';
        }

        if ($prevWeekGoal && empty($this->prevGoalMet)) {
            $this->prevGoalMet = $prevWeekGoal->met ?? array_fill(0, count($prevWeekGoal->goals ?? []), false);
        }

        // Steps completed this week
        $stepsCompleted = Step::where('status', 'done')
            ->whereBetween('completed_at', [$weekStart, $weekEnd])
            ->with('category')
            ->orderBy('completed_at')
            ->get();

        // Focus sessions this week
        $focusSessions = FocusSession::completed()
            ->whereBetween('started_at', [$weekStart, $weekEnd])
            ->with('category')
            ->get();

        $totalFocusMinutes = (int) $focusSessions->sum('duration_minutes');

        // Daily logs this week
        $dailyLogs = DailyLog::whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->orderBy('date')
            ->get();

        $avgRating = $dailyLogs->avg('focus_rating');
        $avgEnergy = $dailyLogs->whereNotNull('energy_level')->avg('energy_level');
        $avgMood = $dailyLogs->whereNotNull('mood')->avg('mood');

        // Per-category breakdown
        $categoryBreakdown = Category::active()->ordered()->get()->map(function ($category) use ($weekStart, $weekEnd) {
            $completed = $category->steps()
                ->where('status', 'done')
                ->whereBetween('completed_at', [$weekStart, $weekEnd])
                ->count();

            $focusMinutes = $category->focusSessions()
                ->completed()
                ->whereBetween('started_at', [$weekStart, $weekEnd])
                ->sum('duration_minutes');

            return [
                'category' => $category,
                'steps_completed' => $completed,
                'focus_minutes' => (int) $focusMinutes,
            ];
        })->filter(fn($item) => $item['steps_completed'] > 0 || $item['focus_minutes'] > 0);

        // Daily activity heatmap data (Mon-Sun)
        $dailyActivity = collect();
        for ($i = 0; $i < 7; $i++) {
            $day = $weekStart->copy()->addDays($i);
            $dayStr = $day->toDateString();

            $steps = Step::where('status', 'done')->whereDate('completed_at', $dayStr)->count();
            $focus = (int) FocusSession::completed()->whereDate('started_at', $dayStr)->sum('duration_minutes');
            $hasLog = DailyLog::where('date', $dayStr)->exists();

            $dailyActivity->push([
                'date' => $day,
                'day_name' => $day->format('D'),
                'steps' => $steps,
                'focus_minutes' => $focus,
                'has_log' => $hasLog,
                'is_active' => $steps > 0 || $focus > 0 || $hasLog,
                'is_future' => $day->isFuture(),
            ]);
        }

        // Compare with previous week
        $prevWeekStart = $weekStart->copy()->subWeek();
        $prevWeekEnd = $prevWeekStart->copy()->endOfWeek();

        $prevStepsCompleted = Step::where('status', 'done')
            ->whereBetween('completed_at', [$prevWeekStart, $prevWeekEnd])
            ->count();

        $prevFocusMinutes = (int) FocusSession::completed()
            ->whereBetween('started_at', [$prevWeekStart, $prevWeekEnd])
            ->sum('duration_minutes');

        // Auto-generated insights
        $insights = $this->generateInsights(
            $stepsCompleted->count(),
            $totalFocusMinutes,
            $prevStepsCompleted,
            $prevFocusMinutes,
            $dailyLogs,
            $dailyActivity
        );

        return view('livewire.weekly-review', [
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'isCurrentWeek' => $isCurrentWeek,
            'stepsCompleted' => $stepsCompleted,
            'totalFocusMinutes' => $totalFocusMinutes,
            'dailyLogs' => $dailyLogs,
            'avgRating' => $avgRating,
            'avgEnergy' => $avgEnergy,
            'avgMood' => $avgMood,
            'categoryBreakdown' => $categoryBreakdown,
            'dailyActivity' => $dailyActivity,
            'prevStepsCompleted' => $prevStepsCompleted,
            'prevFocusMinutes' => $prevFocusMinutes,
            'weeklyGoal' => $weeklyGoal,
            'prevWeekGoal' => $prevWeekGoal,
            'insights' => $insights,
        ]);
    }

    private function generateInsights(
        int $stepsCompleted,
        int $focusMinutes,
        int $prevSteps,
        int $prevFocus,
        $dailyLogs,
        $dailyActivity
    ): array {
        $insights = [];

        // Steps comparison
        if ($prevSteps > 0) {
            $diff = $stepsCompleted - $prevSteps;
            if ($diff > 0) {
                $pct = round(($diff / $prevSteps) * 100);
                $insights[] = "You completed {$pct}% more steps than last week ({$diff} more). Keep it up!";
            } elseif ($diff < 0) {
                $insights[] = "You completed " . abs($diff) . " fewer steps than last week. Consider smaller tasks.";
            } else {
                $insights[] = "Same number of steps as last week. Try pushing for one more.";
            }
        } elseif ($stepsCompleted > 0) {
            $insights[] = "Great start! {$stepsCompleted} steps completed this week.";
        }

        // Focus comparison
        if ($prevFocus > 0) {
            $diff = $focusMinutes - $prevFocus;
            if ($diff < 0) {
                $insights[] = "Focus time dropped by " . abs($diff) . "m. Consider more Pomodoro sessions.";
            } elseif ($diff > 60) {
                $insights[] = "Focus time increased by " . intdiv($diff, 60) . "h " . ($diff % 60) . "m. Excellent!";
            }
        }

        // Consistency
        $activeDays = $dailyActivity->where('is_active', true)->where('is_future', false)->count();
        if ($activeDays >= 5) {
            $insights[] = "Active {$activeDays}/7 days — strong consistency!";
        } elseif ($activeDays <= 2 && !$dailyActivity->where('is_future', false)->isEmpty()) {
            $insights[] = "Only active {$activeDays} days this week. Aim for at least 4.";
        }

        // Energy/mood correlation
        $lowEnergyDays = $dailyLogs->where('energy_level', '<=', 2)->count();
        $highFocusDays = $dailyLogs->where('focus_rating', '>=', 4)->count();
        if ($lowEnergyDays > 2 && $highFocusDays === 0) {
            $insights[] = "Low energy on {$lowEnergyDays} days with no high-focus days. Prioritize sleep and recovery.";
        }

        return $insights;
    }
}
