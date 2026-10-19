<?php

namespace App\Services;

use App\Models\FocusSession;

class FocusStatsService
{
    public function getWeeklyStats(): array
    {
        $days = collect(range(6, 0))->map(fn ($i) => today()->subDays($i));

        $sessions = FocusSession::completed()
            ->where('started_at', '>=', $days->first()->startOfDay())
            ->get();

        $dailyStats = [];
        foreach ($days as $day) {
            $daySessions = $sessions->filter(fn ($s) => $s->started_at->isSameDay($day));
            $seconds = (int) $daySessions->sum(fn ($session) => $session->durationInSeconds());
            $averageSeconds = $daySessions->count() > 0
                ? (int) round($seconds / $daySessions->count())
                : 0;

            $dailyStats[] = [
                'date' => $day->format('D'),
                'sessions' => $daySessions->count(),
                'seconds' => $seconds,
                'minutes' => (int) round($seconds / 60),
                'avg_duration' => $averageSeconds,
                'avg_duration_label' => FocusSession::formatDuration($averageSeconds),
            ];
        }

        $weekSessions = $sessions->count();
        $weekSeconds = (int) $sessions->sum(fn ($session) => $session->durationInSeconds());
        $avgDurationSeconds = $weekSessions > 0 ? (int) round($weekSeconds / $weekSessions) : 0;
        $longestSessionSeconds = (int) $sessions->max(fn ($session) => $session->durationInSeconds());
        $chartMaxSeconds = (int) collect($dailyStats)->max('seconds');
        $dailyStats = collect($dailyStats)->map(function (array $day) use ($chartMaxSeconds) {
            $day['chart_percentage'] = $chartMaxSeconds > 0
                ? (int) round(($day['seconds'] / $chartMaxSeconds) * 100)
                : 0;

            return $day;
        })->all();

        // Trend: compare this week's minutes to last week
        $lastWeekStart = today()->subDays(13);
        $lastWeekEnd = today()->subDays(7);
        $lastWeekSessions = FocusSession::completed()
            ->whereBetween('started_at', [$lastWeekStart->startOfDay(), $lastWeekEnd->endOfDay()])
            ->get();
        $lastWeekSeconds = (int) $lastWeekSessions->sum(fn ($session) => $session->durationInSeconds());

        $trend = 'stable';
        if ($lastWeekSeconds > 0) {
            $change = (($weekSeconds - $lastWeekSeconds) / $lastWeekSeconds) * 100;
            if ($change > 10) {
                $trend = 'up';
            } elseif ($change < -10) {
                $trend = 'down';
            }
        } elseif ($weekSeconds > 0) {
            $trend = 'up';
        }

        // Best day
        $bestDay = collect($dailyStats)->sortByDesc('seconds')->first();

        return [
            'daily' => $dailyStats,
            'week_sessions' => $weekSessions,
            'week_seconds' => $weekSeconds,
            'week_minutes' => (int) round($weekSeconds / 60),
            'week_hours' => round($weekSeconds / 3600, 1),
            'week_duration_label' => FocusSession::formatDuration($weekSeconds),
            'avg_duration' => $avgDurationSeconds,
            'avg_duration_label' => FocusSession::formatDuration($avgDurationSeconds),
            'longest_session' => $longestSessionSeconds,
            'longest_session_label' => FocusSession::formatDuration($longestSessionSeconds),
            'chart_max_seconds' => $chartMaxSeconds,
            'trend' => $trend,
            'best_day' => $bestDay,
            'has_data' => $weekSessions > 0,
        ];
    }
}
