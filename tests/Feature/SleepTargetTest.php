<?php

namespace Tests\Feature;

use App\Models\DailyLog;
use App\Models\Setting;
use App\Services\SleepService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SleepTargetTest extends TestCase
{
    use RefreshDatabase;

    public function test_sleep_targets_can_be_changed_and_are_used_for_sleep_data(): void
    {
        Setting::set('sleep_target_time', '22:30');
        Setting::set('wake_target_time', '06:30');

        DailyLog::create([
            'date' => today()->toDateString(),
            'slept_at' => '22:30',
            'woke_at' => '06:30',
            'sleep_hours' => 8,
            'focus_rating' => 0,
        ]);

        $sleep = app(SleepService::class)->getSleepData();

        $this->assertSame('22:30', $sleep['target_sleep']);
        $this->assertSame('06:30', $sleep['target_wake']);
        $this->assertSame(8.0, $sleep['target_hours']);
        $this->assertSame(100, $sleep['today_score']);
    }
}
