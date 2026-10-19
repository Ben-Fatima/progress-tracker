<?php

namespace App\Services;

use App\Models\DailyLog;
use App\Models\FocusSession;
use App\Models\Step;
use App\Models\Category;
use Illuminate\Support\Collection;

class BatteryService
{
    private const MAX_SCORE = 100;

    /**
     * Calculate the daily battery score and factor breakdown.
     *
     * @return array{score: int, mode: string, mode_color: string, mode_emoji: string, mode_description: string, factors: array, explanation: string}
     */
    public function calculate(?DailyLog $todayLog = null): array
    {
        $log = $todayLog ?? DailyLog::today();
        $factors = [];
        $total = 0;

        // --- Sleep duration (max +18) ---
        $sleepHours = $log?->sleep_hours ? (float) $log->sleep_hours : null;
        if ($sleepHours !== null) {
            $points = match(true) {
                $sleepHours >= 7.5 && $sleepHours <= 9 => 18,
                $sleepHours >= 6.5 && $sleepHours < 7.5 => 14,
                $sleepHours >= 5.5 && $sleepHours < 6.5 => 6,
                $sleepHours > 9 => 10,
                $sleepHours < 5.5 => -8,
                default => 0,
            };
            $factors[] = $this->factor('sleep', 'Sleep', $points, $sleepHours . 'h', $sleepHours >= 6.5);
            $total += $points;
        }

        // --- Sleep quality (max +12) ---
        $sleepQuality = $log?->sleep_quality;
        if ($sleepQuality) {
            $points = match($sleepQuality) {
                5 => 12, 4 => 9, 3 => 4, 2 => -4, 1 => -8, default => 0,
            };
            $factors[] = $this->factor('sleep_quality', 'Sleep quality', $points, $sleepQuality . '/5', $sleepQuality >= 3);
            $total += $points;
        }

        // --- Energy level (max +20) ---
        $energy = $log?->energy_level;
        if ($energy) {
            $points = match($energy) {
                5 => 20, 4 => 15, 3 => 8, 2 => -5, 1 => -12, default => 0,
            };
            $factors[] = $this->factor('energy', 'Energy', $points, $energy . '/5', $energy >= 3);
            $total += $points;
        }

        // --- Mood (max +10) ---
        $mood = $log?->mood;
        if ($mood) {
            $points = match($mood) {
                5 => 10, 4 => 8, 3 => 5, 2 => -3, 1 => -8, default => 0,
            };
            $factors[] = $this->factor('mood', 'Mood', $points, $mood . '/5', $mood >= 3);
            $total += $points;
        }

        // --- Stress level (inverted, max +15) ---
        $stress = $log?->stress_level;
        if ($stress) {
            // 1 = low stress (good), 5 = high stress (bad)
            $points = match($stress) {
                1 => 15, 2 => 12, 3 => 5, 4 => -6, 5 => -12, default => 0,
            };
            $factors[] = $this->factor('stress', 'Stress', $points, $stress . '/5', $stress <= 3);
            $total += $points;
        }

        // --- Training / recovery (max +12) ---
        $trained = $log?->training_done ?? false;
        $recovery = $log?->recovery_quality;
        if ($trained && $recovery && $recovery >= 4) {
            $points = 12;
            $label = 'Trained + recovered';
        } elseif ($trained) {
            $points = 8;
            $label = 'Trained';
        } elseif ($recovery) {
            $points = match($recovery) { 5 => 10, 4 => 7, 3 => 3, 2 => -2, 1 => -5, default => 0 };
            $label = 'Recovery ' . $recovery . '/5';
        } else {
            $points = 0;
            $label = null;
        }
        if ($label) {
            $factors[] = $this->factor('recovery', 'Recovery', $points, $label, $points > 0);
            $total += $points;
        }

        // --- Nutrition (max +8) ---
        $nutrition = $log?->nutrition_quality;
        if ($nutrition) {
            $points = match($nutrition) {
                5 => 8, 4 => 5, 3 => 2, 2 => -3, 1 => -5, default => 0,
            };
            $factors[] = $this->factor('nutrition', 'Nutrition', $points, $nutrition . '/5', $nutrition >= 3);
            $total += $points;
        }

        // --- Recent focus (yesterday's deep work, max +8) ---
        $yesterdayFocus = (int) FocusSession::completed()
            ->whereDate('started_at', today()->subDay())
            ->sum('duration_minutes');
        if ($yesterdayFocus > 0) {
            $points = match(true) {
                $yesterdayFocus >= 120 => 8,
                $yesterdayFocus >= 60 => 5,
                $yesterdayFocus >= 30 => 3,
                default => 1,
            };
            $factors[] = $this->factor('recent_focus', 'Recent focus', $points, intdiv($yesterdayFocus, 60) . 'h ' . ($yesterdayFocus % 60) . 'm', true);
            $total += $points;
        }

        // --- Consistency / streak (max +4) ---
        $streak = app(StreakService::class)->getCurrentStreak();
        if ($streak >= 7) {
            $points = 4;
        } elseif ($streak >= 3) {
            $points = 2;
        } else {
            $points = 0;
        }
        if ($streak > 0) {
            $factors[] = $this->factor('consistency', 'Consistency', $points, $streak . ' day streak', $points > 0);
            $total += $points;
        }

        // If no data at all, return neutral
        if (empty($factors)) {
            return $this->buildResult(50, $factors, 'No data yet today. Log your sleep, energy, and mood to get a readiness score.');
        }

        $score = max(0, min(self::MAX_SCORE, $total + 30)); // base offset of 30 so empty-ish days aren't 0

        $explanation = $this->generateExplanation($factors, $score);

        return $this->buildResult($score, $factors, $explanation);
    }

    private function factor(string $key, string $label, int $points, string $detail, bool $positive): array
    {
        return [
            'key' => $key,
            'label' => $label,
            'points' => $points,
            'detail' => $detail,
            'positive' => $positive,
            'icon' => $this->iconFor($key),
        ];
    }

    private function iconFor(string $key): string
    {
        return match($key) {
            'sleep' => '😴',
            'sleep_quality' => '🛏️',
            'energy' => '⚡',
            'mood' => '😊',
            'stress' => '🧘',
            'recovery' => '🏋️',
            'nutrition' => '🥗',
            'recent_focus' => '🧠',
            'consistency' => '🔥',
            default => '📊',
        };
    }

    private function buildResult(int $score, array $factors, string $explanation): array
    {
        $mode = $this->getMode($score);

        return [
            'score' => $score,
            'mode' => $mode['name'],
            'mode_color' => $mode['color'],
            'mode_emoji' => $mode['emoji'],
            'mode_description' => $mode['description'],
            'factors' => $factors,
            'explanation' => $explanation,
            'has_data' => !empty($factors),
        ];
    }

    private function getMode(int $score): array
    {
        return match(true) {
            $score >= 80 => [
                'name' => 'PUSH',
                'color' => 'green',
                'emoji' => '🟢',
                'description' => 'Best time for difficult technical work. Go for a longer deep-work session.',
            ],
            $score >= 55 => [
                'name' => 'NORMAL',
                'color' => 'yellow',
                'emoji' => '🟡',
                'description' => 'Normal workload. Pick one important task and focus on it.',
            ],
            $score >= 30 => [
                'name' => 'RECOVERY',
                'color' => 'orange',
                'emoji' => '🟠',
                'description' => 'Reduce workload. Lighter learning, maintenance, or recovery tasks.',
            ],
            default => [
                'name' => 'MINIMUM DAY',
                'color' => 'red',
                'emoji' => '🔴',
                'description' => 'Focus on maintaining consistency. Even 10-15 minutes counts.',
            ],
        };
    }

    private function generateExplanation(array $factors, int $score): string
    {
        $positives = array_filter($factors, fn($f) => $f['positive']);
        $negatives = array_filter($factors, fn($f) => !$f['positive']);

        if ($score >= 80) {
            $topFactor = collect($positives)->sortByDesc('points')->first();
            return $topFactor
                ? "Strong readiness — {$topFactor['label']} is giving you a great score. Today is a good day for your hardest technical task."
                : "Strong readiness score. Today is a good day for your hardest technical task.";
        }

        if ($score >= 55) {
            return "Decent readiness. Pick one important task and give it your best focus.";
        }

        if ($score >= 30) {
            $topNegative = collect($negatives)->sortBy('points')->first();
            return $topNegative
                ? "Lower readiness — {$topNegative['label']} is dragging your score. Take it lighter today. Recovery is productive too."
                : "Lower readiness today. Take it lighter — recovery is productive too.";
        }

        return "Tough day. Just do one small thing — 10-15 minutes keeps the streak alive. Tomorrow will be better.";
    }

    /**
     * Recommend the best task based on performance mode.
     */
    public function recommendMission(array $battery): ?array
    {
        $mode = $battery['mode'];

        if ($mode === 'PUSH') {
            // Highest difficulty: in_progress step, or the step with the most sub-steps (complexity proxy)
            $step = Step::where('status', 'in_progress')
                ->withCount('children')
                ->orderByDesc('children_count')
                ->first();
            if (!$step) {
                $step = Step::where('status', 'todo')
                    ->whereNull('parent_id')
                    ->whereHas('category', fn($q) => $q->active()->ordered())
                    ->withCount('children')
                    ->orderByDesc('children_count')
                    ->first();
            }
            $reason = 'High-energy day → prioritize a difficult technical task.';
        } elseif ($mode === 'NORMAL') {
            $step = Step::where('status', 'in_progress')->first()
                ?? Step::where('status', 'todo')
                    ->whereNull('parent_id')
                    ->whereHas('category', fn($q) => $q->active()->ordered())
                    ->first();
            $reason = 'Normal day → focus on one important task.';
        } elseif ($mode === 'RECOVERY') {
            // Lighter task: a step with no children (simple task)
            $step = Step::where('status', 'todo')
                ->whereNull('parent_id')
                ->whereDoesntHave('children')
                ->whereHas('category', fn($q) => $q->active()->ordered())
                ->orderBy('sort_order')
                ->first();
            $reason = 'Recovery day → pick something light. Maintenance counts.';
        } else {
            // Minimum day: shortest/simplest possible task
            $step = Step::where('status', 'todo')
                ->whereNull('parent_id')
                ->whereDoesntHave('children')
                ->whereHas('category', fn($q) => $q->active()->ordered())
                ->orderBy('sort_order')
                ->first();
            $reason = 'Minimum day → just one small thing. 10-15 minutes keeps the streak alive.';
        }

        if (!$step) return null;

        return [
            'step' => $step,
            'reason' => $reason,
            'category' => $step->category,
        ];
    }

    /**
     * Get battery history for the last N days.
     */
    public function getHistory(int $days = 7): Collection
    {
        $history = collect();
        $startDate = today()->subDays($days - 1);

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            $log = DailyLog::where('date', $date->toDateString())->first();

            if ($log) {
                $result = $this->calculate($log);
                $history->push([
                    'date' => $date,
                    'score' => $result['score'],
                    'mode' => $result['mode'],
                    'has_data' => $result['has_data'],
                ]);
            } else {
                $history->push([
                    'date' => $date,
                    'score' => null,
                    'mode' => 'NO DATA',
                    'has_data' => false,
                ]);
            }
        }

        return $history;
    }

    /**
     * Get history statistics.
     */
    public function getHistoryStats(int $days = 7): array
    {
        $history = $this->getHistory($days)->whereNotNull('score')->values();

        if ($history->isEmpty()) {
            return [
                'average' => 0, 'highest' => 0, 'lowest' => 0,
                'push_days' => 0, 'recovery_days' => 0, 'trend' => '→',
            ];
        }

        $scores = $history->pluck('score');
        $pushDays = $history->where('mode', 'PUSH')->count();
        $recoveryDays = $history->whereIn('mode', 'RECOVERY', 'MINIMUM DAY')->count();

        // Trend: compare last 3 days avg vs previous 3 days avg
        $recent = $scores->slice(-3)->avg();
        $previous = $scores->count() > 3 ? $scores->slice(-6, 3)->avg() : $scores->first();
        $trend = $recent > $previous + 3 ? '↑' : ($recent < $previous - 3 ? '↓' : '→');

        return [
            'average' => round($scores->avg()),
            'highest' => $scores->max(),
            'lowest' => $scores->min(),
            'push_days' => $pushDays,
            'recovery_days' => $recoveryDays,
            'trend' => $trend,
        ];
    }
}
