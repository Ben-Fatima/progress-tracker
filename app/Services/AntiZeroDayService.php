<?php

namespace App\Services;

use App\Models\Step;
use App\Models\DailyLog;

class AntiZeroDayService
{
    /**
     * Check whether the Minimum Viable Day should activate.
     * Now delegates to MissionService for the actual task selection.
     */
    public function shouldSuggest(): bool
    {
        $log = DailyLog::today();

        // Trigger on explicit low energy mode
        if ($log?->energy_mode === 'low') return true;

        // Legacy: trigger on low energy/mood from battery fields
        if ($log && ($log->is_low_energy || $log->is_low_mood)) return true;

        return false;
    }

    public function getMinimalTask(): ?Step
    {
        return app(MissionService::class)->getMinimumViableMission()['step'] ?? null;
    }

    public function getSuggestion(): ?array
    {
        if (!$this->shouldSuggest()) return null;

        $mission = app(MissionService::class)->getMinimumViableMission();
        if (!$mission) return null;

        return [
            'message' => "Low energy day. You don't need to win — just maintain momentum.",
            'step' => $mission['step'],
            'category' => $mission['category'],
            'estimated_minutes' => $mission['estimated_minutes'],
            'xp' => $mission['xp'],
        ];
    }
}
