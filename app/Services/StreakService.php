<?php

namespace App\Services;

use App\Models\Step;
use App\Models\FocusSession;
use App\Models\DailyLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class StreakService
{
    public function getCurrentStreak(): int
    {
        $activeDates = $this->getActiveDates();
        if ($activeDates->isEmpty()) return 0;

        $streak = 0;
        $date = today();

        // Grace: if no activity today, start from yesterday
        if (!$activeDates->contains($date->toDateString())) {
            $date = $date->subDay();
        }

        while ($activeDates->contains($date->toDateString())) {
            $streak++;
            $date = $date->subDay();
        }

        return $streak;
    }

    public function getBestStreak(): int
    {
        $activeDates = $this->getActiveDates()->sort()->values();
        if ($activeDates->isEmpty()) return 0;

        $best = 1;
        $current = 1;

        for ($i = 1; $i < $activeDates->count(); $i++) {
            $prev = Carbon::parse($activeDates[$i - 1]);
            $curr = Carbon::parse($activeDates[$i]);

            if ($prev->diffInDays($curr) === 1) {
                $current++;
                $best = max($best, $current);
            } else {
                $current = 1;
            }
        }

        return $best;
    }

    public function getTodayStats(): array
    {
        $focusMinutes = FocusSession::today()->completed()->sum('duration_minutes');
        $stepsCompleted = Step::whereDate('completed_at', today())->count();

        return [
            'focus_minutes' => (int) $focusMinutes,
            'steps_completed' => $stepsCompleted,
        ];
    }

    private function getActiveDates(): Collection
    {
        $stepDates = Step::whereNotNull('completed_at')
            ->selectRaw("DATE(completed_at) as active_date")
            ->pluck('active_date');

        $sessionDates = FocusSession::completed()
            ->selectRaw("DATE(started_at) as active_date")
            ->pluck('active_date');

        $logDates = DailyLog::pluck('date')
            ->map(fn($d) => Carbon::parse($d)->toDateString());

        return $stepDates->merge($sessionDates)->merge($logDates)->unique();
    }
}
