<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\FocusSession;
use App\Models\Setting;
use App\Models\Step;
use App\Services\MissionService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class FocusTimer extends Component
{
    public ?int $selectedCategoryId = null;

    public ?int $selectedStepId = null;

    public string $mode = 'free'; // 'free' or 'pomodoro'

    public ?FocusSession $activeSession = null;

    // Pomodoro state
    public int $pomodoroBlock = 1;

    public bool $onBreak = false;

    public int $workMinutes = 25;

    public int $breakMinutes = 5;

    public int $longBreakMinutes = 15;

    public int $blocksBeforeLongBreak = 4;

    // Settings editing
    public bool $showSettings = false;

    // Post-session feedback
    public bool $showFeedback = false;

    public ?int $feedbackSessionId = null;

    public string $feedbackOutcome = '';

    public int $feedbackDifficulty = 0;

    public function mount(): void
    {
        $this->loadSettings();
        $this->activeSession = FocusSession::running()->first();
        if ($this->activeSession) {
            $this->selectedCategoryId = $this->activeSession->category_id;
            $this->selectedStepId = $this->activeSession->step_id;
            $this->mode = $this->activeSession->type;

            return;
        }

        // Auto-select from query param (e.g. /focus?step=123)
        $stepId = request()->query('step');
        if ($stepId) {
            $step = Step::find($stepId);
            if ($step && $step->status !== 'done') {
                $this->selectedStepId = $step->id;
                $this->selectedCategoryId = $step->category_id;

                return;
            }
        }

    }

    public function updatedSelectedCategoryId($categoryId): void
    {
        // A category change starts a fresh selection; General must never retain a task.
        $this->selectedStepId = null;
    }

    public function loadSettings(): void
    {
        $this->workMinutes = (int) Setting::get('pomodoro_work_minutes', 25);
        $this->breakMinutes = (int) Setting::get('pomodoro_break_minutes', 5);
        $this->longBreakMinutes = (int) Setting::get('pomodoro_long_break_minutes', 15);
        $this->blocksBeforeLongBreak = (int) Setting::get('pomodoro_blocks_before_long_break', 4);
    }

    public function saveSettings(): void
    {
        $this->validate([
            'workMinutes' => 'required|integer|min:1|max:120',
            'breakMinutes' => 'required|integer|min:1|max:60',
            'longBreakMinutes' => 'required|integer|min:1|max:120',
            'blocksBeforeLongBreak' => 'required|integer|min:2|max:10',
        ]);

        Setting::set('pomodoro_work_minutes', $this->workMinutes);
        Setting::set('pomodoro_break_minutes', $this->breakMinutes);
        Setting::set('pomodoro_long_break_minutes', $this->longBreakMinutes);
        Setting::set('pomodoro_blocks_before_long_break', $this->blocksBeforeLongBreak);
        $this->showSettings = false;
    }

    public function setMode(string $mode): void
    {
        if (! $this->activeSession) {
            $this->mode = $mode;
        }
    }

    public function startSession(): void
    {
        if ($this->activeSession) {
            return;
        }

        $this->pomodoroBlock = 1;
        $this->onBreak = false;

        $categoryId = $this->selectedCategoryId ?: null;
        $stepId = $this->selectedStepId;

        if (! $categoryId || ! $stepId || ! Step::whereKey($stepId)->where('category_id', $categoryId)->exists()) {
            $stepId = null;
        }

        $this->activeSession = FocusSession::create([
            'category_id' => $categoryId,
            'step_id' => $stepId,
            'type' => $this->mode,
            'started_at' => now(),
        ]);
    }

    public function pauseSession(): void
    {
        if (! $this->activeSession || $this->activeSession->isPaused()) {
            return;
        }
        $this->activeSession->pause();
        $this->activeSession->refresh();
    }

    public function resumeSession(): void
    {
        if (! $this->activeSession || ! $this->activeSession->isPaused()) {
            return;
        }
        $this->activeSession->resume();
        $this->activeSession->refresh();
    }

    public function stopSession(): void
    {
        if (! $this->activeSession) {
            return;
        }

        $this->activeSession->stop();
        $categoryName = $this->activeSession->category?->name ?? 'General';
        $minutes = $this->activeSession->duration_minutes;
        ActivityLog::log('session_completed', "Focus session: {$minutes}m on {$categoryName}", $this->activeSession, $this->activeSession->category_id);

        // Show feedback form
        $this->feedbackSessionId = $this->activeSession->id;
        $this->showFeedback = true;
        $this->feedbackOutcome = '';
        $this->feedbackDifficulty = 0;

        $this->activeSession = null;
        $this->pomodoroBlock = 1;
        $this->onBreak = false;
    }

    public function submitFeedback(): void
    {
        if (! $this->feedbackSessionId) {
            return;
        }

        $session = FocusSession::find($this->feedbackSessionId);
        if (! $session) {
            return;
        }

        // Calculate XP based on outcome and duration
        $xp = $this->calculateSessionXP($session);

        $session->update([
            'outcome' => $this->feedbackOutcome ?: null,
            'difficulty' => $this->feedbackDifficulty > 0 ? $this->feedbackDifficulty : null,
            'xp_earned' => $xp,
        ]);

        $this->showFeedback = false;
        $this->feedbackSessionId = null;
    }

    public function skipFeedback(): void
    {
        if ($this->feedbackSessionId) {
            $session = FocusSession::find($this->feedbackSessionId);
            if ($session) {
                $xp = $this->calculateSessionXP($session);
                $session->update(['xp_earned' => $xp]);
            }
        }

        $this->showFeedback = false;
        $this->feedbackSessionId = null;
    }

    protected function calculateSessionXP(FocusSession $session): int
    {
        $minutes = $session->duration_minutes ?? 0;
        $xp = 0;

        // Base XP from duration
        if ($minutes >= 60) {
            $xp = 40;
        } elseif ($minutes >= 45) {
            $xp = 30;
        } elseif ($minutes >= 25) {
            $xp = 20;
        } elseif ($minutes >= 15) {
            $xp = 10;
        } else {
            $xp = 5;
        }

        // Bonus for completing mission
        if ($this->feedbackOutcome === 'completed') {
            $xp += 15;
        } elseif ($this->feedbackOutcome === 'progress') {
            $xp += 5;
        }

        return $xp;
    }

    public function cancelSession(): void
    {
        if (! $this->activeSession) {
            return;
        }

        $this->activeSession->delete();
        $this->activeSession = null;
        $this->pomodoroBlock = 1;
        $this->onBreak = false;
    }

    public function completeBlock(): void
    {
        // Called from JS when pomodoro countdown reaches 0
        if ($this->onBreak) {
            // Break ended, start next work block
            $this->onBreak = false;
            $this->pomodoroBlock++;
        } else {
            // Work block ended, start break
            $this->onBreak = true;
        }
    }

    public function skipBreak(): void
    {
        if ($this->onBreak) {
            $this->onBreak = false;
            $this->pomodoroBlock++;
        }
    }

    public function getCurrentBreakMinutes(): int
    {
        if ($this->pomodoroBlock % $this->blocksBeforeLongBreak === 0) {
            return $this->longBreakMinutes;
        }

        return $this->breakMinutes;
    }

    public function render()
    {
        $todaySessions = FocusSession::today()->completed()->with('category')->latest('started_at')->get();
        $totalSeconds = (int) $todaySessions->sum(fn ($session) => $session->durationInSeconds());
        $todayPomodoroCount = FocusSession::today()->completed()->where('type', 'pomodoro')->count();

        // Get today's mission for quick-start
        $mission = app(MissionService::class)->getMission();

        // Available steps for the selected category
        $availableSteps = $this->selectedCategoryId
            ? Step::where('category_id', $this->selectedCategoryId)
                ->whereIn('status', ['todo', 'in_progress'])
                ->whereNull('parent_id')
                ->orderBy('sort_order')
                ->get()
            : collect();

        return view('livewire.focus-timer', [
            'categories' => Category::active()->ordered()->get(),
            'todaySessions' => $todaySessions,
            'totalSeconds' => $totalSeconds,
            'todayPomodoroCount' => $todayPomodoroCount,
            'mission' => $mission,
            'availableSteps' => $availableSteps,
        ]);
    }
}
