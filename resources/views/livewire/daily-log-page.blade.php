<div>
    <h1 class="text-2xl font-bold mb-2">Daily Log</h1>
    <p class="text-sm text-gray-400 mb-8">{{ now()->format('l, M j') }}</p>

    {{-- Anti-zero-day suggestion (hide in evening — no point suggesting tasks at end of day) --}}
    @if($antiZeroDay && now()->hour < 20)
    <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-5 mb-6 dark:from-amber-900/20 dark:to-orange-900/20 dark:border-amber-800">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center shrink-0 dark:bg-amber-900/40">
                <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-amber-800 dark:text-amber-300">{{ $antiZeroDay['message'] }}</p>
                <div class="flex items-center gap-3 mt-2">
                    <span class="text-sm text-amber-700 dark:text-amber-400 font-medium">{{ $antiZeroDay['step']->title }}</span>
                    <a href="{{ route('categories.show', $antiZeroDay['step']->category_id) }}" class="text-xs text-amber-600 hover:underline dark:text-amber-500">Go &rarr;</a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-2 gap-6">

        {{-- ==================== MORNING ==================== --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 dark:bg-gray-900 dark:border-gray-800">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🌅</span>
                    <h2 class="font-semibold text-gray-900 dark:text-gray-100">Morning Check-in</h2>
                </div>
                <div class="flex items-center gap-2">
                    @if($morningSaved)
                        <span class="text-xs text-green-600 dark:text-green-400">Saved</span>
                    @endif
                    <button wire:click="saveMorning" class="px-3 py-1.5 bg-purple-600 text-white text-xs font-medium rounded-lg hover:bg-purple-700 transition-colors">Save</button>
                </div>
            </div>

            {{-- Sleep Schedule --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Slept at</label>
                    <input wire:model.live="sleptAt" type="time" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Woke up at</label>
                    <input wire:model.live="wokeAt" type="time" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
                </div>
            </div>

            {{-- Sleep Duration & Quality --}}
            <div class="grid grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Sleep (hours) @if($sleptAt && $wokeAt)<span class="text-purple-500 text-[10px]">auto</span>@endif</label>
                    <input wire:model.blur="sleepHours" type="number" step="0.5" min="0" max="24" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" placeholder="7.5">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Sleep quality</label>
                    <div class="flex gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <button wire:click="setSleepQuality({{ $i }})"
                                class="w-8 h-8 rounded-lg text-xs font-bold transition-all {{ $sleepQuality === $i ? 'bg-purple-600 text-white scale-110' : 'bg-gray-100 text-gray-500 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700' }}">
                                {{ $i }}
                            </button>
                        @endfor
                    </div>
                    @if($sleepQuality > 0)
                        <input wire:model.blur="sleepNote" wire:change="$set('saved', false)" type="text" placeholder="Why? (late dinner, phone, stress, noise...)"
                            class="mt-2 w-full px-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
                    @endif
                </div>
            </div>

            {{-- Daily Goal --}}
            <div class="mb-5">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Today's main goal</label>
                <input wire:model.blur="dailyGoal" type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" placeholder="What's the one thing you want to accomplish?">
            </div>

            {{-- Today's Tasks --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Today's tasks</label>

                {{-- Task list: project steps + manual tasks together --}}
                <div class="space-y-1 mb-3">
                    {{-- Selected project steps --}}
                    @foreach($selectedSteps as $step)
                        <div class="flex items-center gap-2.5 py-1.5 px-2.5 rounded-lg {{ $step->status === 'done' ? 'bg-green-50 dark:bg-green-900/10' : 'bg-purple-50/50 dark:bg-purple-900/10' }}">
                            <button wire:click="completeStep({{ $step->id }})" class="shrink-0">
                                @if($step->status === 'done')
                                    <span class="flex items-center justify-center w-5 h-5 rounded border-2 border-green-500 bg-green-500">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                @else
                                    <span class="w-5 h-5 rounded border-2 border-purple-300 block dark:border-purple-600"></span>
                                @endif
                            </button>
                            <div class="flex-1 min-w-0">
                                <span class="text-sm {{ $step->status === 'done' ? 'line-through text-gray-400' : 'text-gray-900 dark:text-gray-100' }}">{{ $step->title }}</span>
                                <span class="text-[10px] text-gray-400 ml-1">{{ $step->category?->name }}</span>
                            </div>
                            <button wire:click="toggleStep({{ $step->id }})" class="text-gray-300 hover:text-red-500 dark:text-gray-600 dark:hover:text-red-400 transition-colors shrink-0" title="Remove">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    @endforeach

                    {{-- Manual tasks --}}
                    @foreach($manualTasks as $index => $task)
                        <div class="flex items-center gap-2.5 py-1.5 px-2.5 rounded-lg {{ ($task['done'] ?? false) ? 'bg-green-50 dark:bg-green-900/10' : 'bg-gray-50 dark:bg-gray-800/50' }}">
                            <button wire:click="toggleManualTask({{ $index }})" class="shrink-0">
                                @if($task['done'] ?? false)
                                    <span class="flex items-center justify-center w-5 h-5 rounded border-2 border-green-500 bg-green-500">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                @else
                                    <span class="w-5 h-5 rounded border-2 border-gray-300 block dark:border-gray-600"></span>
                                @endif
                            </button>
                            <span class="flex-1 text-sm {{ ($task['done'] ?? false) ? 'line-through text-gray-400' : 'text-gray-900 dark:text-gray-100' }}">{{ $task['title'] }}</span>
                            <button wire:click="removeManualTask({{ $index }})" class="text-gray-300 hover:text-red-500 dark:text-gray-600 dark:hover:text-red-400 transition-colors shrink-0" title="Remove">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    @endforeach
                </div>

                {{-- Add task: manual input --}}
                <div class="flex gap-2 mb-2">
                    <input wire:model="newManualTask" wire:keydown.enter="addManualTask" type="text" class="flex-1 px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" placeholder="Type a task and press Enter...">
                    <button wire:click="addManualTask" class="px-3 py-1.5 bg-purple-600 text-white text-xs font-medium rounded-lg hover:bg-purple-700 transition-colors">Add</button>
                </div>

                {{-- Add task: from projects --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-1.5 text-xs text-purple-600 dark:text-purple-400 hover:underline">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Pick from projects
                    </button>

                    <div x-show="open" x-cloak @click.outside="open = false" x-transition
                         class="absolute left-0 top-8 z-20 w-96 bg-white border border-gray-200 rounded-xl shadow-lg p-4 dark:bg-gray-900 dark:border-gray-700">

                        <input wire:model.live.debounce.200ms="taskSearch" type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" placeholder="Search tasks..." autofocus>

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
            </div>

            {{-- Skin Check --}}
            <div class="mt-5 pt-5 border-t border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-lg">🪞</span>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Skin Check</h3>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    {{-- Acne level --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Acne level</label>
                        <div class="flex gap-1">
                            @foreach([1 => '😊', 2 => '😐', 3 => '😕', 4 => '😣', 5 => '😩'] as $level => $emoji)
                                <button wire:click="setAcneLevel({{ $level }})"
                                    class="w-8 h-8 rounded-lg text-sm transition-all {{ $acneLevel === $level ? 'bg-purple-600 scale-110 shadow-md' : 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700' }}"
                                    title="{{ ['Clear', 'Mild', 'Moderate', 'Bad', 'Severe'][$level - 1] }}">
                                    {{ $emoji }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- New breakouts --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">New breakouts</label>
                        <div class="flex gap-1">
                            @foreach(['none' => 'None', '1-2' => '1-2', '3-5' => '3-5', '5+' => '5+'] as $val => $label)
                                <button wire:click="setNewBreakouts('{{ $val }}')"
                                    class="px-2.5 py-1.5 rounded-lg text-xs font-medium transition-all {{ $newBreakouts === $val ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700' }}">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Breakout zones --}}
                @if($acneLevel > 1)
                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Breakout zones</label>
                    <div class="flex gap-1.5 flex-wrap">
                        @foreach(['forehead' => 'Forehead', 'cheeks' => 'Cheeks', 'chin' => 'Chin/Jaw', 'nose' => 'Nose'] as $zone => $label)
                            <button wire:click="toggleBreakoutZone('{{ $zone }}')"
                                class="px-2.5 py-1.5 rounded-lg text-xs font-medium transition-all {{ in_array($zone, $breakoutZones) ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Skincare routine toggles --}}
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model.live="amSkincare" wire:change="$set('saved', false)" class="w-4 h-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800">
                        <span class="text-xs text-gray-700 dark:text-gray-300">AM skincare done</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model.live="sunscreen" wire:change="$set('saved', false)" class="w-4 h-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800">
                        <span class="text-xs text-gray-700 dark:text-gray-300">Sunscreen applied</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model.live="pmSkincareLastNight" wire:change="$set('saved', false)" class="w-4 h-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800">
                        <span class="text-xs text-gray-700 dark:text-gray-300">PM skincare last night</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model.live="retinolLastNight" wire:change="$set('saved', false)" class="w-4 h-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800">
                        <span class="text-xs text-gray-700 dark:text-gray-300">Retinol last night</span>
                    </label>
                </div>

                {{-- Period + skin note --}}
                <div class="flex items-center gap-4 mb-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model.live="onPeriod" wire:change="$set('saved', false)" class="w-4 h-4 rounded border-gray-300 text-pink-600 focus:ring-pink-500 dark:border-gray-600 dark:bg-gray-800">
                        <span class="text-xs text-gray-700 dark:text-gray-300">On period</span>
                    </label>
                </div>

                <input wire:model.blur="skinNote" wire:change="$set('saved', false)" type="text" placeholder="Skin note (irritation, new product, ate dairy...)"
                    class="w-full px-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
            </div>
        </div>

        {{-- ==================== EVENING ==================== --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 dark:bg-gray-900 dark:border-gray-800">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🌙</span>
                    <h2 class="font-semibold text-gray-900 dark:text-gray-100">Evening Reflection</h2>
                </div>
                <div class="flex items-center gap-2">
                    @if($eveningSaved)
                        <span class="text-xs text-green-600 dark:text-green-400">Saved</span>
                    @endif
                    <button wire:click="saveEvening" class="px-3 py-1.5 bg-purple-600 text-white text-xs font-medium rounded-lg hover:bg-purple-700 transition-colors">Save</button>
                </div>
            </div>

            {{-- Ratings: Focus / Energy / Mood --}}
            <div class="grid grid-cols-3 gap-4 mb-5">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Focus</label>
                    <div class="flex gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <button wire:click="setRating({{ $i }})"
                                class="w-8 h-8 rounded-lg text-xs font-bold transition-all {{ $focusRating === $i ? 'bg-purple-600 text-white scale-110 shadow-md' : 'bg-gray-100 text-gray-500 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700' }}">
                                {{ $i }}
                            </button>
                        @endfor
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Energy</label>
                    <div class="flex gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <button wire:click="setEnergy({{ $i }})"
                                class="w-8 h-8 rounded-lg text-xs font-bold transition-all {{ $energyLevel === $i ? 'bg-purple-600 text-white scale-110 shadow-md' : 'bg-gray-100 text-gray-500 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700' }}">
                                {{ $i }}
                            </button>
                        @endfor
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Mood</label>
                    <div class="flex gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <button wire:click="setMood({{ $i }})"
                                class="w-8 h-8 rounded-lg text-xs font-bold transition-all {{ $mood === $i ? 'bg-purple-600 text-white scale-110 shadow-md' : 'bg-gray-100 text-gray-500 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700' }}">
                                {{ $i }}
                            </button>
                        @endfor
                    </div>
                    @if($mood > 0)
                        <input wire:model.blur="moodNote" wire:change="$set('saved', false)" type="text" placeholder="Why? (good news, frustration, tired, social...)"
                            class="mt-2 w-full px-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
                    @endif
                </div>
            </div>

            {{-- Stress / Nutrition --}}
            <div class="grid grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Stress <span class="font-normal">(1=calm 5=high)</span></label>
                    <div class="flex gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <button wire:click="setStressLevel({{ $i }})"
                                class="w-8 h-8 rounded-lg text-xs font-bold transition-all {{ $stressLevel === $i ? 'bg-purple-600 text-white scale-110' : 'bg-gray-100 text-gray-500 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700' }}">
                                {{ $i }}
                            </button>
                        @endfor
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Nutrition</label>
                    <div class="flex gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <button wire:click="setNutritionQuality({{ $i }})"
                                class="w-8 h-8 rounded-lg text-xs font-bold transition-all {{ $nutritionQuality === $i ? 'bg-purple-600 text-white scale-110' : 'bg-gray-100 text-gray-500 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700' }}">
                                {{ $i }}
                            </button>
                        @endfor
                    </div>
                </div>
            </div>

            {{-- Training / Recovery --}}
            <div class="grid grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Trained today?</label>
                    <button wire:click="toggleTraining"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $trainingDone ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-500 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700' }}">
                        {{ $trainingDone ? '✓ Yes' : 'No' }}
                    </button>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Recovery</label>
                    <div class="flex gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <button wire:click="setRecoveryQuality({{ $i }})"
                                class="w-8 h-8 rounded-lg text-xs font-bold transition-all {{ $recoveryQuality === $i ? 'bg-purple-600 text-white scale-110' : 'bg-gray-100 text-gray-500 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700' }}">
                                {{ $i }}
                            </button>
                        @endfor
                    </div>
                </div>
            </div>

            {{-- Distractions + Notes --}}
            <div class="mb-5">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Distractions</label>
                <input wire:model.blur="distractions" type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" placeholder="What pulled you away?">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Notes</label>
                <textarea wire:model.blur="notes" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" placeholder="What went well? Blockers?"></textarea>
            </div>
        </div>
    </div>

    {{-- Summary + Save --}}
    <div class="mt-6 bg-white rounded-2xl border border-gray-100 p-5 dark:bg-gray-900 dark:border-gray-800">
        <div class="flex items-center justify-between">
            <div class="flex gap-6 text-sm text-gray-500 dark:text-gray-400">
                <span>Focus: <span class="font-medium text-gray-900 dark:text-gray-100">{{ intdiv($todayFocusMinutes, 60) }}h {{ $todayFocusMinutes % 60 }}m</span></span>
                <span>Steps done: <span class="font-medium text-gray-900 dark:text-gray-100">{{ $todayStepsCompleted }}</span></span>
                @if($selectedSteps->isNotEmpty())
                    <span>Tasks planned: <span class="font-medium text-gray-900 dark:text-gray-100">{{ $selectedSteps->where('status', 'done')->count() }}/{{ $selectedSteps->count() }}</span></span>
                @endif
            </div>
            <div class="flex items-center gap-3">
                @if($saved)
                    <span class="text-sm text-green-600 dark:text-green-400 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Saved
                    </span>
                @endif
                <button wire:click="save" class="px-5 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">Save Log</button>
            </div>
        </div>
    </div>

    {{-- Past logs --}}
    <div class="mt-6 bg-white rounded-2xl border border-gray-100 p-5 dark:bg-gray-900 dark:border-gray-800">
        <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Recent Logs</h2>
        @forelse($pastLogs as $log)
            <div class="py-3 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-800' : '' }}">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $log->date->format('M j, D') }}</span>
                    <div class="flex items-center gap-3">
                        @if($log->slept_at || $log->woke_at)
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">🛏️{{ $log->slept_at ? \Carbon\Carbon::parse($log->slept_at)->format('H:i') : '?' }}→{{ $log->woke_at ? \Carbon\Carbon::parse($log->woke_at)->format('H:i') : '?' }}</span>
                        @endif
                        @if($log->sleep_hours)
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">😴{{ $log->sleep_hours }}h</span>
                        @endif
                        @if($log->energy_level)
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400">E:{{ $log->energy_level }}</span>
                        @endif
                        @if($log->mood)
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">M:{{ $log->mood }}</span>
                        @endif
                        @if($log->focus_rating)
                        <div class="flex items-center gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="w-2 h-2 rounded-full {{ $i <= $log->focus_rating ? 'bg-purple-500' : 'bg-gray-200 dark:bg-gray-700' }}"></span>
                            @endfor
                        </div>
                        @endif
                    </div>
                </div>
                @if($log->daily_goal)
                    <p class="text-xs text-gray-500 dark:text-gray-400">Goal: {{ $log->daily_goal }}</p>
                @endif
                @if($log->notes)
                    <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $log->notes }}</p>
                @endif
            </div>
        @empty
            <p class="text-sm text-gray-400 text-center py-4">No logs yet.</p>
        @endforelse
    </div>
</div>
