<?php

namespace Tests\Feature;

use App\Models\FocusSession;
use App\Services\FocusStatsService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FocusStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_long_sessions_are_formatted_and_daily_chart_is_scaled_to_daily_totals(): void
    {
        Carbon::setTestNow('2026-09-18 12:00:00');

        $this->createSession('2026-09-17 09:00:00', 194 * 60 + 32);
        $this->createSession('2026-09-17 14:00:00', 30 * 60);

        $stats = app(FocusStatsService::class)->getWeeklyStats();

        $thursday = collect($stats['daily'])->firstWhere('date', 'Thu');

        $this->assertSame(194 * 60 + 32 + 30 * 60, $thursday['seconds']);
        $this->assertSame(225, $stats['week_minutes']);
        $this->assertSame('3h 14m 32s', FocusSession::formatDuration(194 * 60 + 32));
        $this->assertSame('3h 44m 32s', $stats['week_duration_label']);
        $this->assertSame($thursday['seconds'], $stats['chart_max_seconds']);
        $this->assertSame(100, $thursday['chart_percentage']);
    }

    private function createSession(string $startedAt, int $durationSeconds): void
    {
        $startedAt = Carbon::parse($startedAt);

        FocusSession::create([
            'type' => 'free',
            'started_at' => $startedAt,
            'ended_at' => $startedAt->copy()->addSeconds($durationSeconds),
            'duration_seconds' => $durationSeconds,
            'duration_minutes' => (int) round($durationSeconds / 60),
        ]);
    }
}
