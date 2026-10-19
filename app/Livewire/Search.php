<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\DailyLog;
use App\Models\Step;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Search extends Component
{
    public string $query = '';

    public function render()
    {
        $results = [
            'categories' => collect(),
            'steps' => collect(),
            'logs' => collect(),
        ];

        if (strlen($this->query) >= 2) {
            $q = '%' . $this->query . '%';

            $results['categories'] = Category::where('name', 'like', $q)
                ->orWhere('description', 'like', $q)
                ->limit(5)
                ->get();

            $results['steps'] = Step::where('title', 'like', $q)
                ->orWhere('notes', 'like', $q)
                ->with('category')
                ->limit(10)
                ->get();

            $results['logs'] = DailyLog::where('notes', 'like', $q)
                ->orWhere('daily_goal', 'like', $q)
                ->orWhere('distractions', 'like', $q)
                ->orderByDesc('date')
                ->limit(5)
                ->get();
        }

        $totalResults = $results['categories']->count() + $results['steps']->count() + $results['logs']->count();

        return view('livewire.search', [
            'results' => $results,
            'totalResults' => $totalResults,
        ]);
    }
}
