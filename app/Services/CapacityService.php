<?php

namespace App\Services;

use App\Models\DailyLog;

class CapacityService
{
    private const STATES = [
        ['min' => 0,  'max' => 30,  'label' => 'Zombie',    'emoji' => "\u{1F9DF}", 'color' => 'red',    'advice' => 'Survival mode. Protect essentials, defer the rest. Short focus blocks only.'],
        ['min' => 31, 'max' => 55,  'label' => 'Human',     'emoji' => "\u{1F610}", 'color' => 'yellow', 'advice' => 'Normal day. Stick to your plan, avoid overcommitting.'],
        ['min' => 56, 'max' => 75,  'label' => 'Charged',   'emoji' => "\u{26A1}",  'color' => 'blue',   'advice' => 'High capacity. Good day for deep work and learning.'],
        ['min' => 76, 'max' => 90,  'label' => 'Beast',     'emoji' => "\u{1F525}", 'color' => 'purple', 'advice' => 'Push day. Take on hard problems and side-project work.'],
        ['min' => 91, 'max' => 100, 'label' => 'Legendary', 'emoji' => "\u{1F451}", 'color' => 'amber',  'advice' => 'Exceptional day. Go all in on what matters most.'],
    ];

    public function getCapacity(?string $override = null): array
    {
        $log = DailyLog::today();

        if (!$log) {
            return ['has_data' => false];
        }

        $calculatedScore = $this->calculateCapacity($log);
        $calculatedState = $this->getState($calculatedScore);

        // Apply manual override if set
        if ($override) {
            $state = $this->getStateByLabel($override);
            if ($state) {
                $score = (int) round(($state['min'] + $state['max']) / 2);
                return [
                    'has_data' => true,
                    'score' => $score,
                    'state' => $state,
                    'factors' => $this->getFactors($log),
                    'is_override' => true,
                    'calculated_score' => $calculatedScore,
                    'calculated_state' => $calculatedState['label'],
                ];
            }
        }

        return [
            'has_data' => true,
            'score' => $calculatedScore,
            'state' => $calculatedState,
            'factors' => $this->getFactors($log),
            'is_override' => false,
        ];
    }

    private function calculateCapacity(DailyLog $log): int
    {
        $score = 0;
        $weight = 0;

        // Sleep duration (40% weight)
        if ($log->sleep_hours) {
            $hours = (float) $log->sleep_hours;
            // 8h = perfect, each hour off loses 12.5 points out of 40
            $sleepScore = max(0, 40 - abs($hours - 8) * 12.5);
            $score += $sleepScore;
            $weight += 40;
        }

        // Sleep schedule adherence (30% weight)
        if ($log->slept_at || $log->woke_at) {
            $sleepService = app(SleepService::class);
            $scheduleScore = $sleepService->calculateScore($log);
            if ($scheduleScore !== null) {
                $score += ($scheduleScore / 100) * 30;
                $weight += 30;
            }
        }

        // Energy level (15% weight) — 1-5 scale
        if ($log->energy_level) {
            $score += (($log->energy_level - 1) / 4) * 15;
            $weight += 15;
        }

        // Mood (15% weight) — 1-5 scale
        if ($log->mood) {
            $score += (($log->mood - 1) / 4) * 15;
            $weight += 15;
        }

        if ($weight === 0) {
            return 50; // default neutral if no data
        }

        // Scale to 100 based on available data
        return (int) round(min(100, ($score / $weight) * 100));
    }

    private function getState(int $score): array
    {
        foreach (self::STATES as $state) {
            if ($score >= $state['min'] && $score <= $state['max']) {
                return $state;
            }
        }
        return self::STATES[0];
    }

    private function getStateByLabel(string $label): ?array
    {
        foreach (self::STATES as $state) {
            if (strtolower($state['label']) === strtolower($label)) {
                return $state;
            }
        }
        return null;
    }

    private function getFactors(DailyLog $log): array
    {
        $factors = [];

        if ($log->sleep_hours) {
            $factors[] = ['label' => 'Sleep', 'value' => $log->sleep_hours . 'h'];
        }
        if ($log->energy_level) {
            $factors[] = ['label' => 'Energy', 'value' => $log->energy_level . '/5'];
        }
        if ($log->mood) {
            $factors[] = ['label' => 'Mood', 'value' => $log->mood . '/5'];
        }
        if ($log->slept_at && $log->woke_at) {
            $factors[] = ['label' => 'Schedule', 'value' => $log->slept_at . '-' . $log->woke_at];
        }

        return $factors;
    }
}
