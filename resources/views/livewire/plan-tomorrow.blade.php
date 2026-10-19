<div>
    <div class="flex items-center justify-between mb-2">
        <div>
            <h1 class="text-2xl font-bold">Plan Tomorrow</h1>
            <p class="text-sm text-gray-400 mt-1">{{ $tomorrowLabel }}</p>
        </div>
        @if($alreadyPlanned)
            <span class="text-xs px-2.5 py-1 rounded-full bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400 font-medium">Already planned</span>
        @endif
    </div>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">Set your goal, pick tasks, and hit save. Your plan will appear in tomorrow's Daily Log automatically.</p>

    <div class="max-w-2xl space-y-6">

        {{-- Tomorrow's Goal --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 dark:bg-gray-900 dark:border-gray-800">
            <div class="flex items-center gap-2 mb-4">
                <span class="text-lg">🎯</span>
                <h2 class="font-semibold text-gray-900 dark:text-gray-100">Tomorrow's Main Goal</h2>
            </div>
            <input wire:model.blur="dailyGoal" type="text"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200"
                placeholder="What's the one thing you want to accomplish tomorrow?">
        </div>

        {{-- Tasks --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 dark:bg-gray-900 dark:border-gray-800">
            <div class="flex items-center gap-2 mb-4">
                <span class="text-lg">📋</span>
                <h2 class="font-semibold text-gray-900 dark:text-gray-100">Tasks for Tomorrow</h2>
            </div>

            {{-- Selected project steps --}}
            @if($selectedSteps->isNotEmpty())
            <div class="space-y-1 mb-4">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">From projects</label>
                @foreach($selectedSteps as $step)
                    <div class="flex items-center gap-2.5 py-1.5 px-2.5 rounded-lg bg-purple-50/50 dark:bg-purple-900/10">
                        <span class="w-5 h-5 rounded border-2 border-purple-300 block dark:border-purple-600 shrink-0"></span>
                        <div class="flex-1 min-w-0">
                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ $step->title }}</span>
                            <span class="text-[10px] text-gray-400 ml-1">{{ $step->category?->name }}</span>
                        </div>
                        <button wire:click="toggleStep({{ $step->id }})" class="text-gray-300 hover:text-red-500 dark:text-gray-600 dark:hover:text-red-400 transition-colors shrink-0" title="Remove">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endforeach
            </div>
            @endif

            {{-- Manual tasks --}}
            @if(!empty($manualTasks))
            <div class="space-y-1 mb-4">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Custom tasks</label>
                @foreach($manualTasks as $index => $task)
                    <div class="flex items-center gap-2.5 py-1.5 px-2.5 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                        <span class="w-5 h-5 rounded border-2 border-gray-300 block dark:border-gray-600 shrink-0"></span>
                        <span class="flex-1 text-sm text-gray-900 dark:text-gray-100">{{ $task['title'] }}</span>
                        <button wire:click="removeManualTask({{ $index }})" class="text-gray-300 hover:text-red-500 dark:text-gray-600 dark:hover:text-red-400 transition-colors shrink-0" title="Remove">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endforeach
            </div>
            @endif

            {{-- Add manual task --}}
            <div class="flex gap-2 mb-3">
                <input wire:model="newManualTask" wire:keydown.enter="addManualTask" type="text"
                    class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200"
                    placeholder="Type a task and press Enter...">
                <button wire:click="addManualTask" class="px-4 py-2 bg-purple-600 text-white text-xs font-medium rounded-lg hover:bg-purple-700 transition-colors">Add</button>
            </div>

            {{-- Pick from projects --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-1.5 text-xs text-purple-600 dark:text-purple-400 hover:underline">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Pick from projects
                </button>

                <div x-show="open" x-cloak @click.outside="open = false" x-transition
                     class="absolute left-0 top-8 z-20 w-96 bg-white border border-gray-200 rounded-xl shadow-lg p-4 dark:bg-gray-900 dark:border-gray-700">

                    <input wire:model.live.debounce.200ms="taskSearch" type="text"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200"
                        placeholder="Search tasks..." autofocus>

                    <div class="max-h-64 overflow-y-auto space-y-3">
                        @foreach($availableSteps as $category)
                            @php
                                $steps = $category->steps;
                                if ($taskSearch) {
                                    $search = strtolower($taskSearch);
                                    $steps = $steps->filter(fn($s) => str_contains(strtolower($s->title), $search));
                                }
                            @endphp
                            @if($steps->isNotEmpty())
                            <div>
                                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide mb-1">{{ $category->name }}</p>
                                @foreach($steps as $step)
                                    <button wire:click="toggleStep({{ $step->id }})" class="flex items-center gap-2 w-full text-left py-1.5 px-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                        @if(in_array($step->id, $selectedStepIds))
                                            <span class="flex items-center justify-center w-4 h-4 rounded border-2 border-purple-500 bg-purple-500 shrink-0">
                                                <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            </span>
                                        @else
                                            <span class="w-4 h-4 rounded border-2 border-gray-200 block dark:border-gray-700 shrink-0"></span>
                                        @endif
                                        <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ $step->title }}</span>
                                        @if($step->status === 'in_progress')
                                            <span class="text-[9px] px-1.5 py-0.5 rounded bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400 shrink-0">active</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            @if(empty($manualTasks) && $selectedSteps->isEmpty())
                <p class="text-xs text-gray-400 mt-3 text-center py-4">No tasks yet. Add manual tasks or pick from your projects above.</p>
            @endif
        </div>

        {{-- Save --}}
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                @php $totalTasks = count($manualTasks) + $selectedSteps->count(); @endphp
                @if($totalTasks > 0)
                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $totalTasks }}</span> task{{ $totalTasks > 1 ? 's' : '' }} planned
                @else
                    No tasks planned yet
                @endif
            </div>
            <div class="flex items-center gap-3">
                @if($saved)
                    <span class="text-sm text-green-600 dark:text-green-400 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Saved &amp; habit checked
                    </span>
                @endif
                <button wire:click="save" class="px-6 py-2.5 bg-purple-600 text-white text-sm font-medium rounded-xl hover:bg-purple-700 transition-colors shadow-sm">
                    Save Plan
                </button>
            </div>
        </div>
    </div>
</div>
