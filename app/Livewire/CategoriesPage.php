<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CategoriesPage extends Component
{
    public string $newCategoryName = '';

    public string $newCategoryDescription = '';

    public string $newCategoryColor = '#3B82F6';

    public string $newCategoryDeadline = '';

    public string $newCategoryPriority = 'normal';

    public bool $showCreateForm = false;

    public ?int $editingCategoryId = null;

    public string $editName = '';

    public string $editDescription = '';

    public string $editColor = '#3B82F6';

    public string $editDeadline = '';

    public string $editPriority = 'normal';

    public function createCategory(): void
    {
        $this->validate([
            'newCategoryName' => 'required|string|max:255',
            'newCategoryDescription' => 'nullable|string',
            'newCategoryColor' => 'required|string|max:7',
            'newCategoryDeadline' => 'nullable|date',
            'newCategoryPriority' => 'in:low,normal,high,critical',
        ]);

        $maxOrder = Category::max('sort_order') ?? 0;

        Category::create([
            'name' => $this->newCategoryName,
            'description' => $this->newCategoryDescription,
            'color' => $this->newCategoryColor,
            'deadline' => $this->newCategoryDeadline ?: null,
            'priority' => $this->newCategoryPriority,
            'sort_order' => $maxOrder + 1,
        ]);

        $this->reset(['newCategoryName', 'newCategoryDescription', 'newCategoryColor', 'newCategoryDeadline', 'newCategoryPriority', 'showCreateForm']);
        $this->newCategoryColor = '#3B82F6';
        $this->newCategoryPriority = 'normal';
    }

    public function startEditing(int $id): void
    {
        $category = Category::findOrFail($id);
        $this->editingCategoryId = $id;
        $this->editName = $category->name;
        $this->editDescription = $category->description ?? '';
        $this->editColor = $category->color;
        $this->editDeadline = $category->deadline?->format('Y-m-d') ?? '';
        $this->editPriority = $category->priority ?? 'normal';
    }

    public function updateCategory(): void
    {
        $this->validate([
            'editName' => 'required|string|max:255',
            'editDescription' => 'nullable|string',
            'editColor' => 'required|string|max:7',
            'editDeadline' => 'nullable|date',
            'editPriority' => 'in:low,normal,high,critical',
        ]);

        $category = Category::findOrFail($this->editingCategoryId);
        $category->update([
            'name' => $this->editName,
            'description' => $this->editDescription,
            'color' => $this->editColor,
            'deadline' => $this->editDeadline ?: null,
            'priority' => $this->editPriority,
        ]);

        $this->editingCategoryId = null;
    }

    public function deleteCategory(int $id): void
    {
        $category = Category::findOrFail($id);

        // Children cascade their own steps via category_id; deleting the parent
        // would otherwise just null their parent_id (nullOnDelete) and orphan them.
        $category->children()->get()->each(fn ($child) => $child->delete());
        $category->delete();
    }

    public function render()
    {
        $allCategories = Category::active()->ordered()->with(['documents', 'children.documents'])->get();

        // Parent categories (have children)
        $parentCategories = $allCategories->filter(fn ($c) => $c->children->isNotEmpty() && $c->parent_id === null);

        // Standalone categories (no parent, no children, not completed)
        $categories = $allCategories->filter(fn ($c) => $c->parent_id === null && $c->children->isEmpty() && $c->progress < 100);

        // Completed standalone categories
        $completedCategories = $allCategories->filter(fn ($c) => $c->parent_id === null && $c->children->isEmpty() && $c->progress === 100);

        return view('livewire.categories-page', [
            'parentCategories' => $parentCategories,
            'categories' => $categories,
            'completedCategories' => $completedCategories,
        ]);
    }
}
