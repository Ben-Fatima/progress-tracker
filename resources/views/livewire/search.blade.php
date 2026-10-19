<div>
    <h1 class="text-2xl font-bold mb-6">Search</h1>

    <div>
        <input wire:model.live.debounce.300ms="query" type="text" autofocus
            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-900 dark:border-gray-800 dark:text-gray-200"
            placeholder="Search steps, categories, logs...">

        @if(strlen($query) >= 2)
            <p class="text-xs text-gray-400 mt-2 mb-4">{{ $totalResults }} result{{ $totalResults !== 1 ? 's' : '' }}</p>

            {{-- Categories --}}
            @if($results['categories']->isNotEmpty())
                <div class="mb-6">
                    <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Categories</h2>
                    @foreach($results['categories'] as $category)
                        <a href="{{ route('categories.show', $category) }}" class="block bg-white rounded-lg border border-gray-200 p-4 mb-2 hover:shadow-sm transition-shadow">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full" style="background-color: {{ $category->color }}"></span>
                                <span class="font-medium text-gray-900">{{ $category->name }}</span>
                            </div>
                            @if($category->description)
                                <p class="text-sm text-gray-500 mt-1 ml-5">{{ $category->description }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Steps --}}
            @if($results['steps']->isNotEmpty())
                <div class="mb-6">
                    <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Steps</h2>
                    @foreach($results['steps'] as $step)
                        <a href="{{ route('categories.show', $step->category_id) }}" class="block bg-white rounded-lg border border-gray-200 p-4 mb-2 hover:shadow-sm transition-shadow">
                            <div class="flex items-center gap-2">
                                @if($step->status === 'done')
                                    <span class="flex items-center justify-center w-4 h-4 rounded border-2 border-green-500 bg-green-500 shrink-0">
                                        <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                @elseif($step->status === 'in_progress')
                                    <span class="flex items-center justify-center w-4 h-4 rounded border-2 border-blue-500 bg-blue-50 shrink-0">
                                        <span class="w-1.5 h-1.5 rounded-sm bg-blue-500"></span>
                                    </span>
                                @else
                                    <span class="w-4 h-4 rounded border-2 border-gray-300 block shrink-0"></span>
                                @endif
                                <span class="text-sm {{ $step->status === 'done' ? 'line-through text-gray-400' : 'text-gray-900' }}">{{ $step->title }}</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1 ml-6">{{ $step->category?->name }}</p>
                            @if($step->notes)
                                <p class="text-xs text-gray-500 mt-1 ml-6 truncate">{{ $step->notes }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Logs --}}
            @if($results['logs']->isNotEmpty())
                <div class="mb-6">
                    <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Daily Logs</h2>
                    @foreach($results['logs'] as $log)
                        <div class="bg-white rounded-lg border border-gray-200 p-4 mb-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-900">{{ $log->date->format('M j, Y') }}</span>
                                <div class="flex items-center gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="w-2 h-2 rounded-full {{ $i <= $log->focus_rating ? 'bg-blue-500' : 'bg-gray-200' }}"></span>
                                    @endfor
                                </div>
                            </div>
                            @if($log->daily_goal)
                                <p class="text-xs text-gray-600 mt-1">Goal: {{ $log->daily_goal }}</p>
                            @endif
                            @if($log->notes)
                                <p class="text-xs text-gray-500 mt-1 truncate">{{ $log->notes }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            @if($totalResults === 0)
                <div class="text-center py-12 text-gray-400">
                    <p>No results found for "{{ $query }}"</p>
                </div>
            @endif
        @elseif(strlen($query) > 0)
            <p class="text-xs text-gray-400 mt-2">Type at least 2 characters to search</p>
        @endif
    </div>
</div>
