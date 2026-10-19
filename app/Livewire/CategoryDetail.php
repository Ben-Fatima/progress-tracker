<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\FocusSession;
use App\Models\Step;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CategoryDetail extends Component
{
    public Category $category;

    public string $filter = 'all';

    public string $newStepTitle = '';

    public ?int $addingSubStepTo = null;

    public string $newSubStepTitle = '';

    public ?int $editingStepId = null;

    public string $editStepTitle = '';

    public string $editStepNotes = '';

    public string $editStepDueDate = '';

    public string $editStepEstimation = '';

    public ?int $expandedStepId = null;

    public function mount(Category $category): void
    {
        $this->category = $category->load('documents');
    }

    public function toggleStatus(int $stepId): void
    {
        $step = Step::findOrFail($stepId);

        if ($step->status === 'done') {
            $step->markAsTodo();
            // If this step has a parent, revert parent too
            if ($step->parent_id) {
                $parent = $step->parent;
                if ($parent->status === 'done') {
                    $parent->markAsTodo();
                }
            }
        } else {
            $step->markAsDone();
            ActivityLog::log('step_completed', "Completed: {$step->title}", $step, $step->category_id);
            // If all siblings are done, auto-complete parent
            if ($step->parent_id) {
                $parent = $step->parent;
                $allDone = $parent->children()->where('status', '!=', 'done')->count() === 0;
                if ($allDone) {
                    $parent->markAsDone();
                }
            }
            // If this is a parent, mark all children as done too
            if ($step->children()->exists()) {
                $step->children()->where('status', '!=', 'done')->each(fn ($child) => $child->markAsDone());
            }
        }

        $this->category->refresh();
    }

    public function setInProgress(int $stepId): void
    {
        $step = Step::findOrFail($stepId);
        $step->update(['status' => 'in_progress', 'completed_at' => null]);
        $this->category->refresh();
    }

    public function startFocus(int $stepId): void
    {
        $step = Step::findOrFail($stepId);
        if ($step->status === 'todo') {
            $step->update(['status' => 'in_progress', 'completed_at' => null]);
        }
        $this->redirect('/focus?step='.$stepId);
    }

    public function addStep(): void
    {
        $this->validate(['newStepTitle' => 'required|string|max:255']);

        $maxOrder = $this->category->steps()->whereNull('parent_id')->max('sort_order') ?? 0;

        Step::create([
            'category_id' => $this->category->id,
            'title' => $this->newStepTitle,
            'sort_order' => $maxOrder + 1,
        ]);

        $this->newStepTitle = '';
        $this->category->refresh();
    }

    public function addSubStep(int $parentId): void
    {
        $this->validate(['newSubStepTitle' => 'required|string|max:255']);

        $maxOrder = Step::where('parent_id', $parentId)->max('sort_order') ?? 0;

        Step::create([
            'category_id' => $this->category->id,
            'parent_id' => $parentId,
            'title' => $this->newSubStepTitle,
            'sort_order' => $maxOrder + 1,
        ]);

        $this->newSubStepTitle = '';
        $this->addingSubStepTo = null;
        $this->category->refresh();
    }

    public function toggleExpand(int $stepId): void
    {
        $this->expandedStepId = $this->expandedStepId === $stepId ? null : $stepId;
    }

    public function saveNotes(int $stepId): void
    {
        $step = Step::findOrFail($stepId);
        $step->update(['notes' => $this->editStepNotes ?: null]);
    }

    public function startEditingStep(int $stepId): void
    {
        $step = Step::findOrFail($stepId);
        $this->editingStepId = $stepId;
        $this->editStepTitle = $step->title;
        $this->editStepNotes = $step->notes ?? '';
        $this->editStepDueDate = $step->due_date?->format('Y-m-d') ?? '';
        $this->editStepEstimation = $step->estimated_minutes ? (string) $step->estimated_minutes : '';
    }

    public function updateStep(): void
    {
        $this->validate([
            'editStepTitle' => 'required|string|max:255',
            'editStepNotes' => 'nullable|string',
            'editStepDueDate' => 'nullable|date',
            'editStepEstimation' => 'nullable|integer|min:1|max:600',
        ]);

        Step::findOrFail($this->editingStepId)->update([
            'title' => $this->editStepTitle,
            'notes' => $this->editStepNotes ?: null,
            'due_date' => $this->editStepDueDate ?: null,
            'estimated_minutes' => $this->editStepEstimation ?: null,
        ]);

        $this->editingStepId = null;
    }

    public function deleteStep(int $stepId): void
    {
        Step::findOrFail($stepId)->delete();
        $this->category->refresh();
    }

    public function render()
    {
        $query = $this->category->steps()->whereNull('parent_id')->orderBy('sort_order');

        if ($this->filter === 'todo') {
            $query->where('status', '!=', 'done');
        } elseif ($this->filter === 'done') {
            $query->where('status', 'done');
        }

        $activeSession = FocusSession::running()->first();
        $focusedStepId = $activeSession?->step_id;

        return view('livewire.category-detail', [
            'steps' => $query->with(['document', 'children' => fn ($q) => $q->orderBy('sort_order')->withSum('focusSessions as actual_minutes', 'duration_minutes')->with('document')])->withSum('focusSessions as actual_minutes', 'duration_minutes')->get(),
            'focusedStepId' => $focusedStepId,
            'subCategories' => $this->category->children()->active()->ordered()->get(),
        ]);
    }
}
