@php
    $fmt = fn ($min) => $min >= 60 ? floor($min / 60) . 'h' . ($min % 60 ? ' ' . ($min % 60) . 'm' : '') : $min . 'm';
@endphp

<div>
    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight dark:text-gray-100">3-Month Plan</h1>
            <p class="text-sm text-gray-500 mt-1 dark:text-gray-400">
                Place tasks on the days you'll actually work on them. This doesn't change deadlines.
            </p>
        </div>
        <div class="flex items-center gap-6 text-right">
            <div>
                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $fmt($plannedMinutes) }}</p>
                <p class="text-[10px] text-gray-500 uppercase tracking-wide">planned</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-400 dark:text-gray-500">{{ $fmt($unplannedMinutes) }}</p>
                <p class="text-[10px] text-gray-500 uppercase tracking-wide">unplanned</p>
            </div>
        </div>
    </div>

    {{-- Selection banner --}}
    @if($selectedStep)
        <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-purple-50 border border-purple-200 rounded-xl dark:bg-purple-900/20 dark:border-purple-800">
            <svg class="w-4 h-4 text-purple-600 shrink-0 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
            <span class="text-sm text-purple-900 dark:text-purple-200">
                <span class="font-medium">{{ $selectedStep->title }}</span>
                &mdash; click days to place it. Click another day to split it across days
                @if($selectedStep->planDays->isNotEmpty())
                    (on {{ $selectedStep->planDays->count() }} {{ Str::plural('day', $selectedStep->planDays->count()) }})
                @endif
            </span>
            <button wire:click="selectStep({{ $selectedStep->id }})" class="ml-auto text-xs text-purple-600 hover:underline dark:text-purple-400">Cancel</button>
        </div>
    @endif

    {{-- Month switcher --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex gap-1 bg-gray-100 dark:bg-gray-800 p-1 rounded-xl w-fit">
            @foreach($months as $m)
                <button wire:click="goToMonth('{{ $m['key'] }}')"
                    class="px-4 py-1.5 rounded-lg text-sm font-medium transition-all {{ $m['isCurrent'] ? 'bg-white text-purple-700 shadow-sm dark:bg-gray-700 dark:text-purple-300' : 'text-gray-500 dark:text-gray-400' }}">
                    {{ $m['label'] }}
                    <span class="ml-1 text-[10px] {{ $m['isCurrent'] ? 'text-purple-500' : 'text-gray-400' }}">{{ $fmt($m['minutes']) }}</span>
                </button>
            @endforeach
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="previousMonth" @disabled(!$canGoBack)
                class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 disabled:opacity-30 disabled:hover:bg-transparent dark:hover:bg-gray-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <span class="text-sm font-medium w-32 text-center dark:text-gray-200">{{ $monthLabel }}</span>
            <button wire:click="nextMonth" @disabled(!$canGoForward)
                class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 disabled:opacity-30 disabled:hover:bg-transparent dark:hover:bg-gray-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
            <button wire:click="clearMonth" wire:confirm="Unschedule every task in {{ $monthLabel }}?"
                class="ml-2 text-xs text-gray-400 hover:text-red-500">Clear month</button>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-5">
        {{-- Calendar --}}
        <div class="xl:col-span-3">
            <div class="grid grid-cols-7 gap-1.5 mb-1.5">
                @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $dow)
                    <div class="text-[10px] font-semibold uppercase tracking-wide text-gray-400 text-center py-1">{{ $dow }}</div>
                @endforeach
            </div>

            <div class="space-y-1.5">
                @foreach($weeks as $week)
                    <div class="grid grid-cols-7 gap-1.5">
                        @foreach($week as $day)
                            @php
                                $ratio = $day['capacity'] > 0 ? $day['minutes'] / $day['capacity'] : 0;
                                $barColor = $ratio > 1 ? 'bg-red-500' : ($ratio > 0.75 ? 'bg-amber-500' : 'bg-green-500');
                            @endphp
                            <div wire:click="handleDayClick('{{ $day['key'] }}')"
                                class="min-h-28 rounded-xl border p-2 transition-all cursor-pointer
                                    {{ $day['inMonth'] ? 'bg-white dark:bg-gray-900' : 'bg-gray-50 dark:bg-gray-950/50' }}
                                    {{ $day['isToday'] ? 'border-purple-400 ring-1 ring-purple-300 dark:border-purple-600' : 'border-gray-100 dark:border-gray-800' }}
                                    {{ $selectedStep ? 'hover:border-purple-400 hover:bg-purple-50/50 dark:hover:bg-purple-900/10' : 'hover:border-purple-300 dark:hover:border-gray-600' }}">

                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-semibold
                                        {{ $day['isToday'] ? 'text-purple-600 dark:text-purple-400' : ($day['inMonth'] ? 'text-gray-700 dark:text-gray-300' : 'text-gray-300 dark:text-gray-700') }}">
                                        {{ $day['date']->day }}
                                    </span>
                                    @if($day['minutes'] > 0)
                                        <span class="text-[9px] {{ $ratio > 1 ? 'text-red-500 font-semibold' : 'text-gray-400' }}">{{ $fmt($day['minutes']) }}</span>
                                    @endif
                                </div>

                                @if($day['minutes'] > 0)
                                    <div class="w-full bg-gray-100 rounded-full h-0.5 mb-1.5 dark:bg-gray-800">
                                        <div class="h-0.5 rounded-full {{ $barColor }}" style="width: {{ min(100, $ratio * 100) }}%"></div>
                                    </div>
                                @endif

                                <div class="space-y-1">
                                    @foreach($day['entries'] as $entry)
                                        @php
                                            $step = $entry->step;
                                            $dayCount = $step->planDays->count();
                                            $dayIndex = $step->planDays->search(fn ($d) => $d->id === $entry->id) + 1;
                                        @endphp
                                        <div class="group/chip relative rounded-md px-1.5 py-1 text-[10px] leading-tight
                                            {{ $step->status === 'done' ? 'opacity-50' : '' }}"
                                            style="background-color: {{ $step->category->color }}1a; border-left: 2px solid {{ $step->category->color }};">
                                            <div class="flex items-start gap-1">
                                                <button wire:click.stop="toggleStatus({{ $step->id }})" class="shrink-0 mt-px" title="Toggle done">
                                                    @if($step->status === 'done')
                                                        <svg class="w-2.5 h-2.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                    @else
                                                        <span class="block w-2.5 h-2.5 rounded-sm border border-gray-300 dark:border-gray-600"></span>
                                                    @endif
                                                </button>
                                                <button wire:click.stop="selectStep({{ $step->id }})"
                                                    class="text-left flex-1 min-w-0 {{ $step->status === 'done' ? 'line-through' : '' }} text-gray-700 dark:text-gray-300 {{ $selectedStep && $selectedStep->id === $step->id ? 'font-bold' : '' }}">
                                                    {{ Str::limit($step->title, 40) }}
                                                </button>
                                                @if($dayCount > 1)
                                                    <span class="shrink-0 text-[8px] px-1 rounded bg-white/60 text-gray-500 dark:bg-gray-800 dark:text-gray-400" title="{{ $fmt($entry->minutes ?? $step->minutesForDay()) }} of {{ $fmt($step->estimated_minutes ?? 0) }} on this day">d{{ $dayIndex }}/{{ $dayCount }}</span>
                                                @endif
                                                <button wire:click.stop="unschedule({{ $step->id }}, '{{ $entry->date->toDateString() }}')"
                                                    class="shrink-0 opacity-0 group-hover/chip:opacity-100 text-gray-400 hover:text-red-500" title="Remove this day">
                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

            <p class="mt-3 text-[10px] text-gray-400">
                Capacity assumed: {{ \App\Livewire\PlanningPage::WEEKDAY_CAPACITY }}m on weekdays,
                {{ \App\Livewire\PlanningPage::WEEKEND_CAPACITY }}m on weekends. Red means you've overbooked the day.
            </p>
        </div>

        {{-- Backlog --}}
        <div class="xl:col-span-1">
            <div class="sticky top-6">
                <h2 class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-3">Unplanned backlog</h2>

                <div class="space-y-2 mb-3">
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search tasks..."
                        class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-900 dark:border-gray-800 dark:text-gray-200">
                    <select wire:model.live="categoryFilter"
                        class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-900 dark:border-gray-800 dark:text-gray-200">
                        <option value="">All categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-3 max-h-[calc(100vh-16rem)] overflow-y-auto pr-1">
                    @forelse($backlog as $categoryName => $steps)
                        <div>
                            <div class="flex items-center gap-1.5 mb-1.5">
                                <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $steps->first()->category->color }}"></span>
                                <p class="text-[10px] font-semibold text-gray-500 truncate dark:text-gray-400">{{ $categoryName }}</p>
                                <span class="text-[9px] text-gray-400 ml-auto shrink-0">{{ $steps->count() }}</span>
                            </div>
                            <div class="space-y-1">
                                @foreach($steps as $step)
                                    <button wire:click="selectStep({{ $step->id }})"
                                        class="w-full text-left px-2 py-1.5 rounded-lg border text-[11px] leading-tight transition-all
                                            {{ $selectedStep && $selectedStep->id === $step->id
                                                ? 'border-purple-400 bg-purple-50 dark:bg-purple-900/20 dark:border-purple-600'
                                                : 'border-gray-100 bg-white hover:border-gray-300 dark:bg-gray-900 dark:border-gray-800 dark:hover:border-gray-700' }}">
                                        <span class="text-gray-700 dark:text-gray-300">{{ $step->title }}</span>
                                        @if($step->estimated_minutes)
                                            <span class="block mt-0.5 text-[9px] text-gray-400">{{ $fmt($step->estimated_minutes) }}</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 text-center py-8">Nothing unplanned here.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Day detail modal --}}
    @if($openDate)
        @php
            $modalDate = \Carbon\CarbonImmutable::parse($openDate);
            $modalMinutes = $dayEntries->sum(fn ($e) => $e->minutes ?? $e->step->minutesForDay());
            $modalCapacity = \App\Livewire\PlanningPage::capacityFor($modalDate);
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" wire:click.self="closeDay">
            <div class="w-full max-w-md bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-gray-100">{{ $modalDate->format('l, F j') }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $fmt($modalMinutes) }} planned · {{ $fmt($modalCapacity) }} capacity</p>
                    </div>
                    <button wire:click="closeDay" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="px-5 py-4 space-y-2 max-h-[60vh] overflow-y-auto">
                    @forelse($dayEntries as $entry)
                        @php
                            $step = $entry->step;
                            $dayCount = $step->planDays->count();
                            $dayIndex = $step->planDays->search(fn ($d) => $d->id === $entry->id) + 1;
                            $entryMinutes = $entry->minutes ?? $step->minutesForDay();
                        @endphp
                        <div class="rounded-xl p-3 {{ $step->status === 'done' ? 'opacity-60' : '' }}"
                            style="background-color: {{ $step->category->color }}12; border-left: 3px solid {{ $step->category->color }};">
                            <div class="flex items-start gap-2">
                                <button wire:click="toggleStatus({{ $step->id }})" class="shrink-0 mt-0.5" title="Toggle done">
                                    @if($step->status === 'done')
                                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    @else
                                        <span class="block w-4 h-4 rounded border-2 border-gray-300 dark:border-gray-600"></span>
                                    @endif
                                </button>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200 {{ $step->status === 'done' ? 'line-through' : '' }}">{{ $step->title }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        {{ $step->category->name }} · {{ $fmt($entryMinutes) }}
                                        @if($dayCount > 1)
                                            · day {{ $dayIndex }}/{{ $dayCount }}
                                        @endif
                                    </p>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    @if($step->status !== 'done')
                                        <a href="{{ route('focus', ['step' => $step->id]) }}"
                                            class="p-1.5 text-gray-400 hover:text-green-600 dark:hover:text-green-400 rounded-lg hover:bg-white/60 dark:hover:bg-gray-800 transition-colors" title="Start focusing">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                                        </a>
                                    @endif
                                    <a href="{{ route('categories.show', $step->category_id) }}"
                                        class="p-1.5 text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 rounded-lg hover:bg-white/60 dark:hover:bg-gray-800 transition-colors" title="Open category">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                    <button wire:click="unschedule({{ $step->id }}, '{{ $entry->date->toDateString() }}')"
                                        class="p-1.5 text-gray-400 hover:text-red-500 rounded-lg hover:bg-white/60 dark:hover:bg-gray-800 transition-colors" title="Remove from this day">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-8">Nothing planned for this day.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
