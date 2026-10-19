<?php

namespace Tests\Feature;

use App\Livewire\FocusTimer;
use App\Models\Category;
use App\Models\Step;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FocusTimerTaskLinkingTest extends TestCase
{
    use RefreshDatabase;

    public function test_general_focus_does_not_auto_assign_an_in_progress_task(): void
    {
        $category = Category::create([
            'name' => 'Test category',
            'color' => '#3B82F6',
            'priority' => 'normal',
        ]);

        Step::create([
            'category_id' => $category->id,
            'title' => 'In-progress task',
            'status' => 'in_progress',
        ]);

        $timer = new FocusTimer;
        $timer->mount();

        $this->assertNull($timer->selectedCategoryId);
        $this->assertNull($timer->selectedStepId);
    }

    public function test_general_focus_session_has_no_task_link_even_with_stale_task_state(): void
    {
        $category = Category::create([
            'name' => 'Test category',
            'color' => '#3B82F6',
            'priority' => 'normal',
        ]);

        $step = Step::create([
            'category_id' => $category->id,
            'title' => 'Previously selected task',
            'status' => 'in_progress',
        ]);

        $timer = new FocusTimer;
        $timer->selectedStepId = $step->id;
        $timer->selectedCategoryId = null;
        $timer->startSession();

        $this->assertDatabaseHas('focus_sessions', [
            'category_id' => null,
            'step_id' => null,
        ]);
    }
}
