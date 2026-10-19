<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Step;
use App\Models\Template;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Templates extends Component
{
    public ?int $savingFromCategoryId = null;
    public string $newTemplateName = '';
    public string $newTemplateDescription = '';
    public ?int $loadingTemplateId = null;
    public string $loadCategoryName = '';
    public string $loadCategoryColor = '#3B82F6';

    public function saveFromCategory(): void
    {
        $this->validate([
            'savingFromCategoryId' => 'required|exists:categories,id',
            'newTemplateName' => 'required|string|max:255',
        ]);

        $category = Category::findOrFail($this->savingFromCategoryId);
        $steps = $category->steps()->whereNull('parent_id')->orderBy('sort_order')
            ->with(['children' => fn($q) => $q->orderBy('sort_order')])
            ->get()
            ->map(function ($step) {
                $item = ['title' => $step->title];
                if ($step->children->isNotEmpty()) {
                    $item['children'] = $step->children->pluck('title')->toArray();
                }
                return $item;
            })->toArray();

        Template::create([
            'name' => $this->newTemplateName,
            'description' => $this->newTemplateDescription ?: null,
            'steps' => $steps,
        ]);

        $this->reset(['savingFromCategoryId', 'newTemplateName', 'newTemplateDescription']);
    }

    public function loadTemplate(): void
    {
        $this->validate([
            'loadingTemplateId' => 'required|exists:templates,id',
            'loadCategoryName' => 'required|string|max:255',
        ]);

        $template = Template::findOrFail($this->loadingTemplateId);
        $maxOrder = Category::max('sort_order') ?? 0;

        $category = Category::create([
            'name' => $this->loadCategoryName,
            'color' => $this->loadCategoryColor,
            'sort_order' => $maxOrder + 1,
        ]);

        foreach ($template->steps as $order => $stepData) {
            $parent = Step::create([
                'category_id' => $category->id,
                'title' => $stepData['title'],
                'sort_order' => $order,
            ]);

            if (!empty($stepData['children'])) {
                foreach ($stepData['children'] as $childOrder => $childTitle) {
                    Step::create([
                        'category_id' => $category->id,
                        'parent_id' => $parent->id,
                        'title' => $childTitle,
                        'sort_order' => $childOrder,
                    ]);
                }
            }
        }

        $this->reset(['loadingTemplateId', 'loadCategoryName', 'loadCategoryColor']);
        $this->loadCategoryColor = '#3B82F6';
    }

    public function deleteTemplate(int $id): void
    {
        Template::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.templates', [
            'templates' => Template::orderBy('name')->get(),
            'categories' => Category::active()->ordered()->get(),
        ]);
    }
}
