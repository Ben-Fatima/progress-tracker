<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\DailyLog;
use App\Models\FocusSession;
use App\Models\Step;
use App\Models\ActivityLog;
use App\Services\AntiZeroDayService;
use App\Services\BatteryService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DailyLogPage extends Component
{
    // Morning
    public string $sleepHours = '';
    public int $sleepQuality = 0;
    public string $sleepNote = '';
    public string $sleptAt = '';
    public string $wokeAt = '';
    public string $dailyGoal = '';
    public array $selectedStepIds = [];
    public array $manualTasks = [];
    public string $newManualTask = '';

    // Skin tracking (morning)
    public int $acneLevel = 0;
    public string $newBreakouts = '';
    public array $breakoutZones = [];
    public bool $amSkincare = false;
    public bool $sunscreen = false;
    public bool $pmSkincareLastNight = false;
    public bool $retinolLastNight = false;
    public bool $onPeriod = false;
    public string $skinNote = '';

    // Evening
    public int $focusRating = 0;
    public int $energyLevel = 0;
    public int $mood = 0;
    public string $moodNote = '';
    public int $stressLevel = 0;
    public bool $trainingDone = false;
    public int $recoveryQuality = 0;
    public int $nutritionQuality = 0;
    public string $distractions = '';
    public string $notes = '';

    public bool $saved = false;
    public bool $morningSaved = false;
    public bool $eveningSaved = false;
    public string $taskSearch = '';

    public function mount(): void
    {
        $log = DailyLog::today();
        if ($log) {
            $this->sleepHours = $log->sleep_hours ? (string) $log->sleep_hours : '';
            $this->sleepQuality = $log->sleep_quality ?? 0;
            $this->sleepNote = $log->sleep_note ?? '';
            $this->sleptAt = $log->slept_at ?? '';
            $this->wokeAt = $log->woke_at ?? '';
            $this->dailyGoal = $log->daily_goal ?? '';
            $this->selectedStepIds = $log->selected_step_ids ?? [];
            $this->manualTasks = $log->key_tasks ?? [];
            $this->acneLevel = $log->acne_level ?? 0;
            $this->newBreakouts = $log->new_breakouts ?? '';
            $this->breakoutZones = $log->breakout_zones ?? [];
            $this->amSkincare = $log->am_skincare ?? false;
            $this->sunscreen = $log->sunscreen ?? false;
            $this->pmSkincareLastNight = $log->pm_skincare_last_night ?? false;
            $this->retinolLastNight = $log->retinol_last_night ?? false;
            $this->onPeriod = $log->on_period ?? false;
            $this->skinNote = $log->skin_note ?? '';
            $this->focusRating = $log->focus_rating ?? 0;
            $this->energyLevel = $log->energy_level ?? 0;
            $this->mood = $log->mood ?? 0;
            $this->moodNote = $log->mood_note ?? '';
            $this->stressLevel = $log->stress_level ?? 0;
            $this->trainingDone = $log->training_done ?? false;
            $this->recoveryQuality = $log->recovery_quality ?? 0;
            $this->nutritionQuality = $log->nutrition_quality ?? 0;
            $this->distractions = $log->distractions ?? '';
            $this->notes = $log->notes ?? '';
        }
    }

    public function updatedSleptAt(): void { $this->calculateSleepHours(); }
    public function updatedWokeAt(): void { $this->calculateSleepHours(); }

    protected function calculateSleepHours(): void
    {
        if ($this->sleptAt && $this->wokeAt) {
            $slept = \Carbon\Carbon::createFromFormat('H:i', $this->sleptAt);
            $woke = \Carbon\Carbon::createFromFormat('H:i', $this->wokeAt);

            // If slept_at is after woke_at, it means sleep crossed midnight
            if ($slept->greaterThanOrEqualTo($woke)) {
                $woke->addDay();
            }

            $hours = round($slept->diffInMinutes($woke) / 60, 1);
            $this->sleepHours = (string) $hours;
        }
    }

    // Rating setters
    public function setRating(int $v): void { $this->focusRating = $v; $this->saved = false; }
    public function setEnergy(int $v): void { $this->energyLevel = $v; $this->saved = false; }
    public function setMood(int $v): void { $this->mood = $v; $this->saved = false; }
    public function setSleepQuality(int $v): void { $this->sleepQuality = $v; $this->saved = false; }
    public function setAcneLevel(int $v): void { $this->acneLevel = $v; $this->saved = false; }
    public function setNewBreakouts(string $v): void { $this->newBreakouts = $v; $this->saved = false; }
    public function toggleBreakoutZone(string $zone): void {
        if (in_array($zone, $this->breakoutZones)) {
            $this->breakoutZones = array_values(array_diff($this->breakoutZones, [$zone]));
        } else {
            $this->breakoutZones[] = $zone;
        }
        $this->saved = false;
    }
    public function setStressLevel(int $v): void { $this->stressLevel = $v; $this->saved = false; }
    public function setRecoveryQuality(int $v): void { $this->recoveryQuality = $v; $this->saved = false; }
    public function setNutritionQuality(int $v): void { $this->nutritionQuality = $v; $this->saved = false; }
    public function toggleTraining(): void { $this->trainingDone = !$this->trainingDone; $this->saved = false; }

    // Manual tasks
    public function addManualTask(): void
    {
        $task = trim($this->newManualTask);
        if ($task !== '') {
            $this->manualTasks[] = ['title' => $task, 'done' => false];
            $this->newManualTask = '';
            $this->saved = false;
        }
    }

    public function toggleManualTask(int $index): void
    {
        if (isset($this->manualTasks[$index])) {
            $this->manualTasks[$index]['done'] = !$this->manualTasks[$index]['done'];
            $this->saved = false;
        }
    }

    public function removeManualTask(int $index): void
    {
        unset($this->manualTasks[$index]);
        $this->manualTasks = array_values($this->manualTasks);
        $this->saved = false;
    }

    // Task selection from projects
    public function toggleStep(int $stepId): void
    {
        if (in_array($stepId, $this->selectedStepIds)) {
            $this->selectedStepIds = array_values(array_filter($this->selectedStepIds, fn($id) => $id !== $stepId));
        } else {
            $this->selectedStepIds[] = $stepId;
        }
        $this->saved = false;
    }

    public function completeStep(int $stepId): void
    {
        $step = Step::find($stepId);
        if ($step && $step->status !== 'done') {
            $step->markAsDone();
            ActivityLog::log('step_completed', "Completed: {$step->title}", $step, $step->category_id);
        }
    }

    public function saveMorning(): void
    {
        $this->validate([
            'sleepHours' => 'nullable|numeric|min:0|max:24',
            'sleepQuality' => 'nullable|integer|min:0|max:5',
            'sleptAt' => 'nullable|date_format:H:i',
            'wokeAt' => 'nullable|date_format:H:i',
            'dailyGoal' => 'nullable|string|max:255',
        ]);

        $log = $this->findOrCreateTodayLog();
        $log->fill([
            'daily_goal' => $this->dailyGoal ?: null,
            'key_tasks' => !empty($this->manualTasks) ? $this->manualTasks : null,
            'selected_step_ids' => !empty($this->selectedStepIds) ? $this->selectedStepIds : null,
            'sleep_hours' => $this->sleepHours !== '' ? $this->sleepHours : null,
            'sleep_quality' => $this->sleepQuality > 0 ? $this->sleepQuality : null,
            'sleep_note' => $this->sleepNote ?: null,
            'slept_at' => $this->sleptAt ?: null,
            'woke_at' => $this->wokeAt ?: null,
            'acne_level' => $this->acneLevel > 0 ? $this->acneLevel : null,
            'new_breakouts' => $this->newBreakouts ?: null,
            'breakout_zones' => !empty($this->breakoutZones) ? $this->breakoutZones : null,
            'am_skincare' => $this->amSkincare,
            'sunscreen' => $this->sunscreen,
            'pm_skincare_last_night' => $this->pmSkincareLastNight,
            'retinol_last_night' => $this->retinolLastNight,
            'on_period' => $this->onPeriod,
            'skin_note' => $this->skinNote ?: null,
        ])->save();

        ActivityLog::log('daily_log_morning', 'Morning check-in saved');
        $this->morningSaved = true;
        $this->saved = false;
    }

    public function saveEvening(): void
    {
        $this->validate([
            'focusRating' => 'nullable|integer|min:0|max:5',
            'energyLevel' => 'nullable|integer|min:0|max:5',
            'mood' => 'nullable|integer|min:0|max:5',
            'stressLevel' => 'nullable|integer|min:0|max:5',
            'recoveryQuality' => 'nullable|integer|min:0|max:5',
            'nutritionQuality' => 'nullable|integer|min:0|max:5',
            'distractions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $log = $this->findOrCreateTodayLog();
        $log->fill([
            'focus_rating' => $this->focusRating > 0 ? $this->focusRating : null,
            'energy_level' => $this->energyLevel > 0 ? $this->energyLevel : null,
            'mood' => $this->mood > 0 ? $this->mood : null,
            'mood_note' => $this->moodNote ?: null,
            'stress_level' => $this->stressLevel > 0 ? $this->stressLevel : null,
            'training_done' => $this->trainingDone,
            'recovery_quality' => $this->recoveryQuality > 0 ? $this->recoveryQuality : null,
            'nutrition_quality' => $this->nutritionQuality > 0 ? $this->nutritionQuality : null,
            'distractions' => $this->distractions ?: null,
            'notes' => $this->notes ?: null,
        ])->save();

        ActivityLog::log('daily_log_evening', 'Evening reflection saved');
        $this->eveningSaved = true;
        $this->saved = false;
    }

    public function save(): void
    {
        $this->validate([
            'sleepHours' => 'nullable|numeric|min:0|max:24',
            'sleepQuality' => 'nullable|integer|min:0|max:5',
            'sleptAt' => 'nullable|date_format:H:i',
            'wokeAt' => 'nullable|date_format:H:i',
            'dailyGoal' => 'nullable|string|max:255',
            'focusRating' => 'nullable|integer|min:0|max:5',
            'energyLevel' => 'nullable|integer|min:0|max:5',
            'mood' => 'nullable|integer|min:0|max:5',
            'stressLevel' => 'nullable|integer|min:0|max:5',
            'recoveryQuality' => 'nullable|integer|min:0|max:5',
            'nutritionQuality' => 'nullable|integer|min:0|max:5',
            'distractions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $log = $this->findOrCreateTodayLog();
        $log->fill([
            'daily_goal' => $this->dailyGoal ?: null,
            'key_tasks' => !empty($this->manualTasks) ? $this->manualTasks : null,
            'selected_step_ids' => !empty($this->selectedStepIds) ? $this->selectedStepIds : null,
            'sleep_hours' => $this->sleepHours !== '' ? $this->sleepHours : null,
            'sleep_quality' => $this->sleepQuality > 0 ? $this->sleepQuality : null,
            'sleep_note' => $this->sleepNote ?: null,
            'slept_at' => $this->sleptAt ?: null,
            'woke_at' => $this->wokeAt ?: null,
            'acne_level' => $this->acneLevel > 0 ? $this->acneLevel : null,
            'new_breakouts' => $this->newBreakouts ?: null,
            'breakout_zones' => !empty($this->breakoutZones) ? $this->breakoutZones : null,
            'am_skincare' => $this->amSkincare,
            'sunscreen' => $this->sunscreen,
            'pm_skincare_last_night' => $this->pmSkincareLastNight,
            'retinol_last_night' => $this->retinolLastNight,
            'on_period' => $this->onPeriod,
            'skin_note' => $this->skinNote ?: null,
            'focus_rating' => $this->focusRating > 0 ? $this->focusRating : null,
            'energy_level' => $this->energyLevel > 0 ? $this->energyLevel : null,
            'mood' => $this->mood > 0 ? $this->mood : null,
            'mood_note' => $this->moodNote ?: null,
            'stress_level' => $this->stressLevel > 0 ? $this->stressLevel : null,
            'training_done' => $this->trainingDone,
            'recovery_quality' => $this->recoveryQuality > 0 ? $this->recoveryQuality : null,
            'nutrition_quality' => $this->nutritionQuality > 0 ? $this->nutritionQuality : null,
            'distractions' => $this->distractions ?: null,
            'notes' => $this->notes ?: null,
        ])->save();

        ActivityLog::log('daily_log_saved', 'Full daily log saved');
        $this->saved = true;
        $this->morningSaved = false;
        $this->eveningSaved = false;
    }

    protected function findOrCreateTodayLog(): DailyLog
    {
        return DailyLog::whereDate('date', today()->toDateString())->first()
            ?? new DailyLog(['date' => today()->toDateString()]);
    }

    public function render()
    {
        $todayFocusMinutes = (int) FocusSession::today()->completed()->sum('duration_minutes');
        $todayStepsCompleted = Step::whereDate('completed_at', today())->count();
        $antiZeroDay = app(AntiZeroDayService::class)->getSuggestion();
        $battery = app(BatteryService::class)->calculate();

        // Available steps for task picker: todo + in_progress, grouped by category
        $availableSteps = Category::active()->ordered()->with(['steps' => function ($q) {
            $q->whereIn('status', ['todo', 'in_progress'])
              ->whereNull('parent_id')
              ->orderBy('sort_order');
        }])->get()->filter(fn($c) => $c->steps->isNotEmpty());

        // Selected steps (load full models for display, include done ones so they show as checked)
        $selectedSteps = !empty($this->selectedStepIds)
            ? Step::with('category')->whereIn('id', $this->selectedStepIds)->get()
            : collect();

        return view('livewire.daily-log-page', [
            'todayFocusMinutes' => $todayFocusMinutes,
            'todayStepsCompleted' => $todayStepsCompleted,
            'pastLogs' => DailyLog::recent(7)->get(),
            'antiZeroDay' => $antiZeroDay,
            'battery' => $battery,
            'availableSteps' => $availableSteps,
            'selectedSteps' => $selectedSteps,
        ]);
    }
}
