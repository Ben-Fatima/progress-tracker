<?php

namespace App\Livewire;

use App\Models\SavingsGoal;
use App\Models\SavingsTransaction;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class SavingsTracker extends Component
{
    // Goal form
    public bool $showGoalForm = false;
    public ?int $editingGoalId = null;
    public string $goalTitle = '';
    public string $goalDescription = '';
    public string $targetAmount = '';
    public string $goalColor = '#7C3AED';
    public string $targetDate = '';

    // Transaction form
    public ?int $activeGoalId = null;
    public string $transactionType = 'deposit';
    public string $transactionAmount = '';
    public string $transactionDate = '';
    public string $transactionNote = '';

    public function mount(): void
    {
        $this->transactionDate = today()->toDateString();
    }

    public function createGoal(): void
    {
        $this->validate([
            'goalTitle' => 'required|string|max:255',
            'targetAmount' => 'required|numeric|min:0.01',
            'goalDescription' => 'nullable|string|max:500',
            'goalColor' => 'required|string|max:7',
            'targetDate' => 'nullable|date|after:today',
        ]);

        SavingsGoal::create([
            'title' => $this->goalTitle,
            'description' => $this->goalDescription ?: null,
            'target_amount' => $this->targetAmount,
            'color' => $this->goalColor,
            'target_date' => $this->targetDate ?: null,
        ]);

        $this->resetGoalForm();
    }

    public function editGoal(int $id): void
    {
        $goal = SavingsGoal::findOrFail($id);
        $this->editingGoalId = $id;
        $this->goalTitle = $goal->title;
        $this->goalDescription = $goal->description ?? '';
        $this->targetAmount = (string) $goal->target_amount;
        $this->goalColor = $goal->color;
        $this->targetDate = $goal->target_date?->toDateString() ?? '';
        $this->showGoalForm = true;
    }

    public function updateGoal(): void
    {
        $this->validate([
            'goalTitle' => 'required|string|max:255',
            'targetAmount' => 'required|numeric|min:0.01',
            'goalDescription' => 'nullable|string|max:500',
            'goalColor' => 'required|string|max:7',
            'targetDate' => 'nullable|date',
        ]);

        $goal = SavingsGoal::findOrFail($this->editingGoalId);
        $goal->update([
            'title' => $this->goalTitle,
            'description' => $this->goalDescription ?: null,
            'target_amount' => $this->targetAmount,
            'color' => $this->goalColor,
            'target_date' => $this->targetDate ?: null,
        ]);

        $this->resetGoalForm();
    }

    public function deleteGoal(int $id): void
    {
        SavingsGoal::findOrFail($id)->delete();
        if ($this->activeGoalId === $id) {
            $this->activeGoalId = null;
        }
    }

    public function selectGoal(int $id): void
    {
        $this->activeGoalId = $this->activeGoalId === $id ? null : $id;
    }

    public function addTransaction(): void
    {
        $this->validate([
            'activeGoalId' => 'required|exists:savings_goals,id',
            'transactionType' => 'required|in:deposit,withdrawal',
            'transactionAmount' => 'required|numeric|min:0.01',
            'transactionDate' => 'required|date',
            'transactionNote' => 'nullable|string|max:255',
        ]);

        $amount = (float) $this->transactionAmount;
        if ($this->transactionType === 'withdrawal') {
            $amount = -$amount;
        }

        SavingsTransaction::create([
            'savings_goal_id' => $this->activeGoalId,
            'amount' => $amount,
            'type' => $this->transactionType,
            'date' => $this->transactionDate,
            'note' => $this->transactionNote ?: null,
        ]);

        $this->reset(['transactionAmount', 'transactionNote']);
        $this->transactionDate = today()->toDateString();
    }

    public function deleteTransaction(int $id): void
    {
        SavingsTransaction::findOrFail($id)->delete();
    }

    public function resetGoalForm(): void
    {
        $this->reset(['showGoalForm', 'editingGoalId', 'goalTitle', 'goalDescription', 'targetAmount', 'goalColor', 'targetDate']);
        $this->goalColor = '#7C3AED';
    }

    public function render()
    {
        $goals = SavingsGoal::with('transactions')->orderByDesc('created_at')->get();
        $activeGoal = $this->activeGoalId ? SavingsGoal::with(['transactions' => fn($q) => $q->latest('date')->latest('id')->limit(20)])->find($this->activeGoalId) : null;

        $totalSaved = $goals->sum(fn($g) => $g->current_balance);
        $totalTarget = $goals->sum(fn($g) => (float) $g->target_amount);

        return view('livewire.savings-tracker', [
            'goals' => $goals,
            'activeGoal' => $activeGoal,
            'totalSaved' => $totalSaved,
            'totalTarget' => $totalTarget,
        ]);
    }
}
