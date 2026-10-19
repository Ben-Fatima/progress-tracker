<?php

namespace App\Livewire;

use App\Models\Habit;
use App\Models\HabitLog;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class HabitTracker extends Component
{
    public bool $showForm = false;
    public ?int $editingId = null;
    public string $habitName = '';
    public string $habitIcon = '';
    public string $habitFrequency = 'daily';

    public function toggleHabit(int $habitId, string $date): void
    {
        $existing = HabitLog::where('habit_id', $habitId)->whereDate('date', $date)->first();

        if ($existing) {
            $existing->delete();
        } else {
            HabitLog::create([
                'habit_id' => $habitId,
                'date' => $date,
                'completed' => true,
            ]);
        }
    }

    public function createHabit(): void
    {
        $this->validate([
            'habitName' => 'required|string|max:255',
            'habitIcon' => 'nullable|string|max:10',
            'habitFrequency' => 'in:daily,weekly',
        ]);

        $maxOrder = Habit::max('sort_order') ?? 0;

        Habit::create([
            'name' => $this->habitName,
            'icon' => $this->habitIcon ?: '',
            'frequency' => $this->habitFrequency,
            'sort_order' => $maxOrder + 1,
        ]);

        $this->resetForm();
    }

    public function editHabit(int $id): void
    {
        $habit = Habit::findOrFail($id);
        $this->editingId = $id;
        $this->habitName = $habit->name;
        $this->habitIcon = $habit->icon;
        $this->habitFrequency = $habit->frequency;
        $this->showForm = true;
    }

    public function updateHabit(): void
    {
        $this->validate([
            'habitName' => 'required|string|max:255',
            'habitIcon' => 'nullable|string|max:10',
            'habitFrequency' => 'in:daily,weekly',
        ]);

        Habit::findOrFail($this->editingId)->update([
            'name' => $this->habitName,
            'icon' => $this->habitIcon ?: '',
            'frequency' => $this->habitFrequency,
        ]);

        $this->resetForm();
    }

    public function deleteHabit(int $id): void
    {
        Habit::findOrFail($id)->delete();
    }

    public function toggleActive(int $id): void
    {
        $habit = Habit::findOrFail($id);
        $habit->update(['active' => !$habit->active]);
    }

    public function resetForm(): void
    {
        $this->reset(['showForm', 'editingId', 'habitName', 'habitIcon', 'habitFrequency']);
    }

    public function render()
    {
        $habits = Habit::active()->ordered()->get();
        $dates = collect(range(6, 0))->map(fn($i) => today()->subDays($i)->toDateString());

        // Preload logs for efficiency
        $logs = HabitLog::whereIn('habit_id', $habits->pluck('id'))
            ->where('date', '>=', $dates->first())
            ->where('date', '<=', $dates->last() . ' 23:59:59')
            ->get()
            ->groupBy(fn($log) => $log->habit_id . '-' . $log->date->toDateString());

        // Calculate streaks
        $streaks = [];
        foreach ($habits as $habit) {
            $streaks[$habit->id] = $habit->currentStreak();
        }

        // Today's completion rate
        $todayStr = today()->toDateString();
        $totalHabits = $habits->count();
        $completedToday = $totalHabits > 0
            ? $habits->filter(fn($h) => $logs->has($h->id . '-' . $todayStr))->count()
            : 0;

        return view('livewire.habit-tracker', [
            'habits' => $habits,
            'dates' => $dates,
            'logs' => $logs,
            'streaks' => $streaks,
            'completedToday' => $completedToday,
            'totalHabits' => $totalHabits,
        ]);
    }
}
