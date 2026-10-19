<?php

namespace App\Services;

use App\Models\DailyLog;
use App\Models\FocusSession;
use Illuminate\Support\Collection;

class PatternService
{
    /**
     * Find personal patterns from historical data.
     * Only returns patterns when there's enough data (min 7 logs).
     *
     * @return array of pattern strings with emoji prefixes
     */
    public function getPatterns(): array
    {
        $logs = DailyLog::whereNotNull('focus_rating')
            ->orderBy('date')
            ->limit(90)
            ->get();

        if ($logs->count() < 7) {
            return [];
        }

        $patterns = [];

        // Pattern 1: Sleep vs Focus
        $goodSleep = $logs->where('sleep_hours', '>=', 7);
        $poorSleep = $logs->where('sleep_hours', '<', 6)->whereNotNull('sleep_hours');
        if ($goodSleep->count() >= 3 && $poorSleep->count() >= 3) {
            $goodAvg = $goodSleep->avg('focus_rating');
            $poorAvg = $poorSleep->avg('focus_rating');
            if ($goodAvg && $poorAvg && $goodAvg > $poorAvg) {
                $pct = round(($goodAvg - $poorAvg) / $poorAvg * 100);
                if ($pct >= 10) {
                    $patterns[] = "😴 On days with 7+ hours of sleep, your average focus is {$pct}% higher.";
                }
            }
        }

        // Pattern 2: Training vs Mood
        $trainedDays = $logs->where('training_done', true);
        $restDays = $logs->where('training_done', false)->whereNotNull('mood');
        if ($trainedDays->count() >= 3 && $restDays->count() >= 3) {
            $trainedMood = $trainedDays->whereNotNull('mood')->avg('mood');
            $restMood = $restDays->avg('mood');
            if ($trainedMood && $restMood && $trainedMood > $restMood) {
                $patterns[] = "🏋️ Training days correlate with better mood for you.";
            }
        }

        // Pattern 3: Stress vs Focus
        $highStress = $logs->where('stress_level', '>=', 4)->whereNotNull('stress_level');
        $lowStress = $logs->where('stress_level', '<=', 2)->whereNotNull('stress_level');
        if ($highStress->count() >= 3 && $lowStress->count() >= 3) {
            $highFocus = $highStress->avg('focus_rating');
            $lowFocus = $lowStress->avg('focus_rating');
            if ($highFocus && $lowFocus && $highFocus < $lowFocus) {
                $patterns[] = "😵 High-stress days tend to reduce your deep-work time.";
            }
        }

        // Pattern 4: Energy vs Focus
        $highEnergy = $logs->where('energy_level', '>=', 4)->whereNotNull('energy_level');
        $lowEnergy = $logs->where('energy_level', '<=', 2)->whereNotNull('energy_level');
        if ($highEnergy->count() >= 3 && $lowEnergy->count() >= 3) {
            $highFocus = $highEnergy->avg('focus_rating');
            $lowFocus = $lowEnergy->avg('focus_rating');
            if ($highFocus && $lowFocus && $highFocus > $lowFocus) {
                $pct = round(($highFocus - $lowFocus) / $lowFocus * 100);
                if ($pct >= 10) {
                    $patterns[] = "⚡ High-energy days boost your focus rating by {$pct}%.";
                }
            }
        }

        // Pattern 5: Nutrition vs Energy
        $goodNutrition = $logs->where('nutrition_quality', '>=', 4)->whereNotNull('nutrition_quality');
        $poorNutrition = $logs->where('nutrition_quality', '<=', 2)->whereNotNull('nutrition_quality');
        if ($goodNutrition->count() >= 3 && $poorNutrition->count() >= 3) {
            $goodEnergy = $goodNutrition->whereNotNull('energy_level')->avg('energy_level');
            $poorEnergy = $poorNutrition->whereNotNull('energy_level')->avg('energy_level');
            if ($goodEnergy && $poorEnergy && $goodEnergy > $poorEnergy) {
                $patterns[] = "🥗 Better nutrition days correlate with higher energy levels.";
            }
        }

        // Pattern 6: Consistency streak effect
        $streak = app(StreakService::class)->getCurrentStreak();
        if ($streak >= 7) {
            $recentLogs = $logs->sortByDesc('date')->take(7);
            $avgFocus = $recentLogs->avg('focus_rating');
            $olderLogs = $logs->sortByDesc('date')->slice(7, 7);
            $olderAvg = $olderLogs->avg('focus_rating');
            if ($avgFocus && $olderAvg && $avgFocus > $olderAvg) {
                $patterns[] = "🔥 Your focus rating has been improving as your streak grows ({$streak} days).";
            }
        }

        return $patterns;
    }
}
