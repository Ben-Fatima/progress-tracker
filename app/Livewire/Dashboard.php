<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\DailyLog;
use App\Models\Setting;
use App\Models\Step;
use App\Services\CapacityService;
use App\Services\DailyFactService;
use App\Services\FocusStatsService;
use App\Services\MissionService;
use App\Services\MomentumService;
use App\Services\SleepService;
use App\Services\StreakService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public ?string $energyMode = null;

    public ?string $capacityOverride = null;

    public bool $showCapacityAdjust = false;

    public bool $showSleepSettings = false;

    public string $targetSleep = '23:00';

    public string $targetWake = '07:00';

    public function mount(): void
    {
        $log = DailyLog::today();
        $this->energyMode = $log?->energy_mode;
        $this->capacityOverride = $log?->capacity_override;
        $this->targetSleep = (string) Setting::get('sleep_target_time', '23:00');
        $this->targetWake = (string) Setting::get('wake_target_time', '07:00');
    }

    public function setCapacityOverride(string $state): void
    {
        $this->capacityOverride = $state;
        $this->showCapacityAdjust = false;

        $log = DailyLog::whereDate('date', today()->toDateString())->first()
            ?? new DailyLog(['date' => today()->toDateString()]);
        $log->capacity_override = $state;
        $log->save();
    }

    public function clearCapacityOverride(): void
    {
        $this->capacityOverride = null;
        $this->showCapacityAdjust = false;

        $log = DailyLog::today();
        if ($log) {
            $log->capacity_override = null;
            $log->save();
        }
    }

    public function saveSleepTargets(): void
    {
        $this->validate([
            'targetSleep' => 'required|date_format:H:i',
            'targetWake' => 'required|date_format:H:i',
        ]);

        Setting::set('sleep_target_time', $this->targetSleep);
        Setting::set('wake_target_time', $this->targetWake);
        $this->showSleepSettings = false;
    }

    public function setEnergyMode(string $mode): void
    {
        $this->energyMode = $mode;

        // Save to today's daily log
        $log = DailyLog::whereDate('date', today()->toDateString())->first()
            ?? new DailyLog(['date' => today()->toDateString()]);
        $log->energy_mode = $mode;
        $log->save();
    }

    public function render()
    {
        $streakService = app(StreakService::class);
        $missionService = app(MissionService::class);
        $momentumService = app(MomentumService::class);
        $sleepService = app(SleepService::class);
        $factService = app(DailyFactService::class);
        $capacityService = app(CapacityService::class);
        $focusStatsService = app(FocusStatsService::class);

        // Lightweight category summary for dashboard
        $allCategories = Category::active()->ordered()->with('children')->get();
        $topLevelCategories = $allCategories->filter(fn ($c) => $c->parent_id === null);

        $mission = $missionService->getMission($this->energyMode);
        $momentum = $momentumService->calculate();
        $todayXP = $momentumService->getTodayXP();

        // Overdue and due-soon steps
        $overdueSteps = Step::overdue()->with('category')->orderBy('due_date')->limit(5)->get();
        $dueSoonSteps = Step::dueSoon()->with('category')->orderBy('due_date')->limit(5)->get();

        // Recent activity
        $recentActivity = ActivityLog::with('category')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        return view('livewire.dashboard', [
            'topLevelCategories' => $topLevelCategories,
            'currentStreak' => $streakService->getCurrentStreak(),
            'bestStreak' => $streakService->getBestStreak(),
            'todayStats' => $streakService->getTodayStats(),
            'mission' => $mission,
            'momentum' => $momentum,
            'todayXP' => $todayXP,
            'overdueSteps' => $overdueSteps,
            'dueSoonSteps' => $dueSoonSteps,
            'recentActivity' => $recentActivity,
            'sleep' => $sleepService->getSleepData(),
            'capacity' => $capacityService->getCapacity($this->capacityOverride),
            'focusStats' => $focusStatsService->getWeeklyStats(),
            'dailyFact' => $factService->getTodayFact(),
        ]);
    }
}
