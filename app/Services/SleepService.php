<?php

namespace App\Services;

use App\Models\DailyLog;
use App\Models\Setting;

class SleepService
{
    private const TARGET_SLEEP = '23:00';

    private const TARGET_WAKE = '07:00';

    private const TARGET_HOURS = 8.0;

    // Chart range: 21:00 to 11:00 next day (14 hours)
    private const CHART_START = 21; // 21:00

    private const CHART_SPAN = 14;  // 14 hours → ends at 11:00

    public function getSleepData(): array
    {
        $logs = DailyLog::recent(7)->get()->reverse()->values();

        $targetSleep = $this->targetSleep();
        $targetWake = $this->targetWake();

        $days = [];
        foreach ($logs as $log) {
            $sleptAt = $log->slept_at;
            $wokeAt = $log->woke_at;

            $days[] = [
                'date' => $log->date->format('D d/m'),
                'slept_at' => $sleptAt,
                'woke_at' => $wokeAt,
                'sleep_hours' => $log->sleep_hours ? (float) $log->sleep_hours : null,
                'score' => $this->calculateScore($log),
                'sleep_pos' => $sleptAt ? $this->timeToChartPercent($sleptAt) : null,
                'wake_pos' => $wokeAt ? $this->timeToChartPercent($wokeAt) : null,
            ];
        }

        $today = DailyLog::today();
        $todayScore = $today ? $this->calculateScore($today) : null;

        return [
            'days' => $days,
            'today_score' => $todayScore,
            'avg_score' => $this->averageScore($logs),
            'target_sleep' => $targetSleep,
            'target_wake' => $targetWake,
            'target_hours' => round($this->circularTimeDiff($targetSleep, $targetWake) / 60, 1),
            'target_sleep_pos' => $this->timeToChartPercent($targetSleep),
            'target_wake_pos' => $this->timeToChartPercent($targetWake),
            'has_data' => $logs->filter(fn ($l) => $l->slept_at || $l->woke_at)->isNotEmpty(),
        ];
    }

    public function targetSleep(): string
    {
        return (string) Setting::get('sleep_target_time', self::TARGET_SLEEP);
    }

    public function targetWake(): string
    {
        return (string) Setting::get('wake_target_time', self::TARGET_WAKE);
    }

    /**
     * Convert a time string to a percentage position on the chart.
     * Chart runs from 21:00 (0%) to 11:00 next day (100%).
     */
    private function timeToChartPercent(string $time): float
    {
        $parts = explode(':', $time);
        $hour = (int) $parts[0] + ((int) $parts[1]) / 60;

        // Shift so 21:00 = 0
        $shifted = $hour - self::CHART_START;
        if ($shifted < 0) {
            $shifted += 24;
        } // wrap: e.g. 04:00 → 4-21+24 = 7

        return min(100, max(0, round(($shifted / self::CHART_SPAN) * 100, 1)));
    }

    public function calculateScore(DailyLog $log): ?int
    {
        if (! $log->slept_at && ! $log->woke_at) {
            return null;
        }

        $score = 0;
        $factors = 0;

        if ($log->slept_at) {
            // 0 min off = 50, 60 min off = 25, 120+ min off = 0
            $diffMin = $this->circularTimeDiff($log->slept_at, $this->targetSleep());
            $score += max(0, 50 - ($diffMin / 2.4));
            $factors++;
        }

        if ($log->woke_at) {
            $diffMin = $this->circularTimeDiff($log->woke_at, $this->targetWake());
            $score += max(0, 50 - ($diffMin / 2.4));
            $factors++;
        }

        if ($factors === 1) {
            $score *= 2;
        }

        return (int) round(min(100, $score));
    }

    /**
     * Shortest distance between two times on a 24-hour clock, in minutes.
     */
    private function circularTimeDiff(string $a, string $b): int
    {
        $aMin = $this->timeToMinutes($a);
        $bMin = $this->timeToMinutes($b);
        $diff = abs($aMin - $bMin);

        return min($diff, 1440 - $diff); // 1440 = 24*60
    }

    private function timeToMinutes(string $time): int
    {
        $parts = explode(':', $time);

        return (int) $parts[0] * 60 + (int) $parts[1];
    }

    private function averageScore($logs): ?int
    {
        $scored = $logs->map(fn ($l) => $this->calculateScore($l))->filter(fn ($s) => $s !== null);

        return $scored->isNotEmpty() ? (int) round($scored->avg()) : null;
    }
}
