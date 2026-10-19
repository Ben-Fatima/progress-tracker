<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\DailyLog;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\Step;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PlanTomorrow extends Component
{
    public string $dailyGoal = '';
    public array $manualTasks = [];
    public string $newManualTask = '';
    public array $selectedStepIds = [];
    public string $taskSearch = '';

    public bool $saved = false;
    public bool $alreadyPlanned = false;

    public function mount(): void
    {
        $tomorrowLog = DailyLog::whereDate('date', today()->addDay()->toDateString())->first();

        if ($tomorrowLog) {
            $this->dailyGoal = $tomorrowLog->daily_goal ?? '';
            $this->manualTasks = $tomorrowLog->key_tasks ?? [];
            $this->selectedStepIds = $tomorrowLog->selected_step_ids ?? [];
            $this->alreadyPlanned = true;
        }
    }

    public function addManualTask(): void
    {
        $task = trim($this->newManualTask);
        if ($task !== '') {
            $this->manualTasks[] = ['title' => $task, 'done' => false];
            $this->newManualTask = '';
            $this->saved = false;
        }
    }

    public function removeManualTask(int $index): void
    {
        unset($this->manualTasks[$index]);
        $this->manualTasks = array_values($this->manualTasks);
        $this->saved = false;
    }

    public function toggleStep(int $stepId): void
    {
        if (in_array($stepId, $this->selectedStepIds)) {
            $this->selectedStepIds = array_values(array_filter(
                $this->selectedStepIds,
                fn ($id) => $id !== $stepId
            ));
        } else {
            $this->selectedStepIds[] = $stepId;
        }
        $this->saved = false;
    }

    public function save(): void
    {
        $this->validate([
            'dailyGoal' => 'nullable|string|max:255',
        ]);

        $tomorrow = today()->addDay()->toDateString();

        $log = DailyLog::whereDate('date', $tomorrow)->first()
            ?? new DailyLog(['date' => $tomorrow]);

        $log->fill([
            'daily_goal' => $this->dailyGoal ?: null,
            'key_tasks' => ! empty($this->manualTasks) ? $this->manualTasks : null,
            'selected_step_ids' => ! empty($this->selectedStepIds) ? $this->selectedStepIds : null,
        ]);

        $log->save();

        // Auto-check "Plan tomorrow" habit for today
        $habit = Habit::where('name', 'like', '%Plan tomorrow%')->first();
        if ($habit) {
            HabitLog::firstOrCreate(
                ['habit_id' => $habit->id, 'date' => today()->toDateString()],
                ['completed' => true]
            );
        }

        ActivityLog::log('plan_tomorrow', 'Planned tomorrow: ' . ($this->dailyGoal ?: 'no goal set'));

        $this->saved = true;
        $this->alreadyPlanned = true;
    }

    public function render()
    {
        $availableSteps = Category::active()->ordered()
            ->whereNull('parent_id')
            ->orWhereHas('parent', fn ($q) => $q->whereNull('archived_at'))
            ->with(['steps' => function ($q) {
                $q->whereIn('status', ['todo', 'in_progress'])
                    ->whereNull('parent_id')
                    ->orderBy('sort_order');
            }])
            ->get()
            ->filter(fn ($c) => $c->steps->isNotEmpty());

        $selectedSteps = ! empty($this->selectedStepIds)
            ? Step::with('category')->whereIn('id', $this->selectedStepIds)->get()
            : collect();

        $tomorrowLabel = today()->addDay()->format('l, M j');

        return view('livewire.plan-tomorrow', [
            'availableSteps' => $availableSteps,
            'selectedSteps' => $selectedSteps,
            'tomorrowLabel' => $tomorrowLabel,
        ]);
    }
}
