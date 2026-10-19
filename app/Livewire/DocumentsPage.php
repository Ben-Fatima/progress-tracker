<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Document;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DocumentsPage extends Component
{
    // Create form
    public bool $showForm = false;

    public ?int $editingId = null;

    public string $title = '';

    public string $description = '';

    public string $filePath = '';

    public string $url = '';

    public string $type = 'note';

    public ?int $categoryId = null;

    // Filter
    public string $filterType = '';

    public ?int $filterCategory = null;

    public function createDocument(): void
    {
        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'filePath' => 'nullable|string|max:500',
            'url' => 'nullable|string|max:500',
            'type' => 'required|in:note,plan,reference,checklist',
            'categoryId' => 'nullable|exists:categories,id',
        ]);

        Document::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?: null,
            'file_path' => $validated['filePath'] ?: null,
            'url' => $validated['url'] ?: null,
            'type' => $validated['type'],
            'category_id' => $validated['categoryId'],
        ]);

        $this->resetForm();
    }

    public function startEditing(int $id): void
    {
        $doc = Document::findOrFail($id);
        $this->editingId = $doc->id;
        $this->title = $doc->title;
        $this->description = $doc->description ?? '';
        $this->filePath = $doc->file_path ?? '';
        $this->url = $doc->url ?? '';
        $this->type = $doc->type;
        $this->categoryId = $doc->category_id;
        $this->showForm = true;
    }

    public function updateDocument(): void
    {
        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'filePath' => 'nullable|string|max:500',
            'url' => 'nullable|string|max:500',
            'type' => 'required|in:note,plan,reference,checklist',
            'categoryId' => 'nullable|exists:categories,id',
        ]);

        $doc = Document::findOrFail($this->editingId);
        $doc->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?: null,
            'file_path' => $validated['filePath'] ?: null,
            'url' => $validated['url'] ?: null,
            'type' => $validated['type'],
            'category_id' => $validated['categoryId'],
        ]);

        $this->resetForm();
    }

    public function deleteDocument(int $id): void
    {
        Document::findOrFail($id)->delete();
    }

    public function resetForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->title = '';
        $this->description = '';
        $this->filePath = '';
        $this->url = '';
        $this->type = 'note';
        $this->categoryId = null;
    }

    public function render()
    {
        $query = Document::with('category')->latest();

        if ($this->filterType) {
            $query->where('type', $this->filterType);
        }
        if ($this->filterCategory) {
            $query->where('category_id', $this->filterCategory);
        }

        $documents = $query->get();
        $categories = Category::active()->ordered()->whereNull('parent_id')->get();
        $allCategories = Category::active()->ordered()->get();

        // Group documents by category
        $grouped = $documents->groupBy(fn ($doc) => $doc->category_id ?? 'uncategorized');

        return view('livewire.documents-page', [
            'documents' => $documents,
            'categories' => $categories,
            'allCategories' => $allCategories,
            'grouped' => $grouped,
            'types' => Document::TYPES,
        ]);
    }
}
