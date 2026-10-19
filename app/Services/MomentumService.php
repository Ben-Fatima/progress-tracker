<?php

namespace App\Services;

use App\Models\DailyLog;
use App\Models\FocusSession;
use App\Models\Step;
use Illuminate\Support\Carbon;

class MomentumService
{
    /**
     * Calculate momentum score (0-100).
     * Unlike streak (binary), momentum decays slowly on bad days.
     */
    public function calculate(): array
    {
        $consistency = $this->getConsistencyScore();
        $focus = $this->getFocusScore();
        $execution = $this->getExecutionScore();
        $recovery = $this->getRecoveryScore();

        // Weighted composite
        $score = (int) round(
            $consistency * 0.30 +
            $focus * 0.25 +
            $execution * 0.30 +
            $recovery * 0.15
        );

        $score = max(0, min(100, $score));

        return [
            'score' => $score,
            'factors' => [
                ['label' => 'Consistency', 'score' => $consistency, 'weight' => 30],
                ['label' => 'Focus', 'score' => $focus, 'weight' => 25],
                ['label' => 'Execution', 'score' => $execution, 'weight' => 30],
                ['label' => 'Recovery', 'score' => $recovery, 'weight' => 15],
            ],
            'trend' => $this->getTrend(),
            'level' => $this->getLevel($score),
        ];
    }

    /**
     * Consistency: how many of the last 7 days were active.
     */
    protected function getConsistencyScore(): int
    {
        $activeDays = 0;

        for ($i = 0; $i < 7; $i++) {
            $date = today()->subDays($i)->toDateString();
            $hasActivity = $this->dayHasActivity($date);
            if ($hasActivity) $activeDays++;
        }

        // 7/7 = 100, 6/7 = 90, 5/7 = 75, 4/7 = 60, 3/7 = 45, etc.
        // Slightly generous curve: 5 days = solid
        return match(true) {
            $activeDays >= 7 => 100,
            $activeDays >= 6 => 90,
            $activeDays >= 5 => 75,
            $activeDays >= 4 => 60,
            $activeDays >= 3 => 45,
            $activeDays >= 2 => 30,
            $activeDays >= 1 => 15,
            default => 0,
        };
    }

    /**
     * Focus: average daily focus minutes over last 7 days.
     */
    protected function getFocusScore(): int
    {
        $totalMinutes = (int) FocusSession::completed()
            ->where('started_at', '>=', today()->subDays(7))
            ->sum('duration_minutes');

        $avgDaily = $totalMinutes / 7;

        // 60+ min/day = 100, 45 = 85, 30 = 65, 15 = 40, 0 = 0
        return match(true) {
            $avgDaily >= 60 => 100,
            $avgDaily >= 45 => 85,
            $avgDaily >= 30 => 65,
            $avgDaily >= 20 => 50,
            $avgDaily >= 10 => 35,
            $avgDaily >= 5 => 20,
            default => 0,
        };
    }

    /**
     * Execution: steps completed in last 7 days.
     */
    protected function getExecutionScore(): int
    {
        $completed = Step::where('completed_at', '>=', today()->subDays(7))->count();

        // 10+ = 100, 7 = 85, 5 = 70, 3 = 50, 1 = 25
        return match(true) {
            $completed >= 10 => 100,
            $completed >= 7 => 85,
            $completed >= 5 => 70,
            $completed >= 3 => 50,
            $completed >= 2 => 35,
            $completed >= 1 => 25,
            default => 0,
        };
    }

    /**
     * Recovery: how well you bounce back after missing days.
     * Looks at gaps in the last 14 days and whether activity resumed after.
     */
    protected function getRecoveryScore(): int
    {
        $activeDays = [];
        for ($i = 0; $i < 14; $i++) {
            $date = today()->subDays($i)->toDateString();
            $activeDays[$date] = $this->dayHasActivity($date);
        }

        // Count gaps and recoveries
        $gaps = 0;
        $recoveries = 0;
        $inGap = false;

        $dates = array_reverse(array_keys($activeDays));
        foreach ($dates as $date) {
            if (!$activeDays[$date]) {
                if (!$inGap) {
                    $gaps++;
                    $inGap = true;
                }
            } else {
                if ($inGap) {
                    $recoveries++;
                    $inGap = false;
                }
            }
        }

        // No gaps = perfect recovery
        if ($gaps === 0) return 100;

        // All gaps recovered from = great
        if ($gaps > 0 && $recoveries >= $gaps) return 85;

        // Some recovery
        if ($recoveries > 0) return 60;

        // Currently in a gap with no recovery yet
        if ($inGap) return 30;

        return 50;
    }

    /**
     * Check if a specific date had any activity.
     */
    protected function dayHasActivity(string $date): bool
    {
        // Step completed
        if (Step::whereDate('completed_at', $date)->exists()) return true;

        // Focus session
        if (FocusSession::completed()->whereDate('started_at', $date)->exists()) return true;

        // Daily log
        if (DailyLog::whereDate('date', $date)->exists()) return true;

        return false;
    }

    /**
     * Get momentum trend: comparing last 7 days vs previous 7 days.
     */
    protected function getTrend(): string
    {
        $recent = 0;
        $previous = 0;

        for ($i = 0; $i < 7; $i++) {
            if ($this->dayHasActivity(today()->subDays($i)->toDateString())) $recent++;
        }
        for ($i = 7; $i < 14; $i++) {
            if ($this->dayHasActivity(today()->subDays($i)->toDateString())) $previous++;
        }

        if ($recent > $previous) return 'rising';
        if ($recent < $previous) return 'declining';
        return 'steady';
    }

    /**
     * Simple level based on total XP.
     */
    public function getLevel(int $momentum): array
    {
        // XP from all completed focus sessions
        $totalXP = (int) FocusSession::completed()->sum('xp_earned');

        // Add base XP from completed steps (30 each)
        $totalXP += Step::where('status', 'done')->count() * 30;

        // Add daily log XP (10 each)
        $totalXP += DailyLog::count() * 10;

        // Level formula: each level needs slightly more XP
        $level = 1;
        $xpForNext = 100;
        $remaining = $totalXP;

        while ($remaining >= $xpForNext) {
            $remaining -= $xpForNext;
            $level++;
            $xpForNext = 100 + ($level * 20);
        }

        return [
            'level' => $level,
            'xp' => $totalXP,
            'xp_in_level' => $remaining,
            'xp_for_next' => $xpForNext,
            'progress' => $xpForNext > 0 ? (int) round(($remaining / $xpForNext) * 100) : 0,
        ];
    }

    /**
     * Get total XP earned today.
     */
    public function getTodayXP(): int
    {
        $sessionXP = (int) FocusSession::completed()
            ->whereDate('started_at', today())
            ->sum('xp_earned');

        $stepsXP = Step::whereDate('completed_at', today())->count() * 30;

        return $sessionXP + $stepsXP;
    }
}
