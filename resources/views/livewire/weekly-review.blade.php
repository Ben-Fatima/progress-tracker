<div>
    {{-- Week navigation --}}
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold">Weekly Review</h1>
        <div class="flex items-center gap-3">
            <button wire:click="previousWeek" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 dark:hover:bg-gray-700 dark:text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 min-w-48 text-center">
                {{ $weekStart->format('M j') }} &mdash; {{ $weekEnd->format('M j, Y') }}
                @if($isCurrentWeek)
                    <span class="ml-1 px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full">This week</span>
                @endif
            </span>
            <button wire:click="nextWeek" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 dark:hover:bg-gray-700 dark:text-gray-400 {{ $isCurrentWeek ? 'opacity-30 cursor-not-allowed' : '' }}" {{ $isCurrentWeek ? 'disabled' : '' }}>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>

    {{-- Auto-generated insights --}}
    @if(!empty($insights))
    <div class="bg-gradient-to-r from-purple-50 to-purple-100 border border-purple-200 rounded-2xl p-5 mb-6 dark:from-purple-900/20 dark:to-purple-800/20 dark:border-purple-800">
        <h2 class="font-semibold text-purple-900 dark:text-purple-300 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Insights
        </h2>
        <ul class="space-y-1.5">
            @foreach($insights as $insight)
                <li class="text-sm text-purple-800 dark:text-purple-300 flex items-start gap-2">
                    <span class="text-purple-400 mt-0.5">&bull;</span>
                    <span>{{ $insight }}</span>
                </li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Summary cards --}}
    <div class="grid grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl border border-gray-100 p-5 dark:bg-gray-900 dark:border-gray-800">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Steps Completed</p>
            <p class="text-3xl font-bold tracking-tight dark:text-gray-100">{{ $stepsCompleted->count() }}</p>
            @if($prevStepsCompleted > 0)
                @php $stepsDiff = $stepsCompleted->count() - $prevStepsCompleted; @endphp
                <p class="text-xs mt-1 {{ $stepsDiff >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-500' }}">
                    {{ $stepsDiff >= 0 ? '+' : '' }}{{ $stepsDiff }} vs last week
                </p>
            @endif
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5 dark:bg-gray-900 dark:border-gray-800">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Focus Time</p>
            <p class="text-3xl font-bold tracking-tight dark:text-gray-100">{{ intdiv($totalFocusMinutes, 60) }}h {{ $totalFocusMinutes % 60 }}m</p>
            @if($prevFocusMinutes > 0)
                @php $focusDiff = $totalFocusMinutes - $prevFocusMinutes; @endphp
                <p class="text-xs mt-1 {{ $focusDiff >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-500' }}">
                    {{ $focusDiff >= 0 ? '+' : '' }}{{ $focusDiff }}m vs last week
                </p>
            @endif
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5 dark:bg-gray-900 dark:border-gray-800">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Avg Focus</p>
            <p class="text-3xl font-bold tracking-tight dark:text-gray-100">{{ $avgRating ? number_format($avgRating, 1) : '&mdash;' }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $dailyLogs->count() }} / 7 days</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5 dark:bg-gray-900 dark:border-gray-800">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Energy / Mood</p>
            <p class="text-3xl font-bold tracking-tight dark:text-gray-100">{{ $avgEnergy ? number_format($avgEnergy, 1) : '?' }} <span class="text-gray-300">/</span> {{ $avgMood ? number_format($avgMood, 1) : '?' }}</p>
        </div>
    </div>

    {{-- Daily activity strip --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5 mb-8 dark:bg-gray-900 dark:border-gray-800">
        <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Daily Activity</h2>
        <div class="grid grid-cols-7 gap-2">
            @foreach($dailyActivity as $day)
                <div class="text-center">
                    <p class="text-xs font-medium text-gray-500 mb-2 dark:text-gray-400">{{ $day['day_name'] }}</p>
                    <div class="mx-auto w-10 h-10 rounded-lg flex items-center justify-center text-sm font-medium
                        {{ $day['is_future'] ? 'bg-gray-50 text-gray-300 dark:bg-gray-700' : ($day['is_active'] ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-400 dark:bg-gray-700 dark:text-gray-500') }}">
                        {{ $day['date']->format('j') }}
                    </div>
                    @if(!$day['is_future'])
                        <div class="mt-2 space-y-0.5">
                            @if($day['steps'] > 0)
                                <p class="text-[10px] text-gray-500 dark:text-gray-400">{{ $day['steps'] }} steps</p>
                            @endif
                            @if($day['focus_minutes'] > 0)
                                <p class="text-[10px] text-gray-500 dark:text-gray-400">{{ $day['focus_minutes'] }}m</p>
                            @endif
                            @if($day['has_log'])
                                <p class="text-[10px] text-blue-500">logged</p>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Previous week goal review --}}
    @if($prevWeekGoal && !empty($prevWeekGoal->goals))
    <div class="bg-white rounded-2xl border border-gray-100 p-5 mb-6 dark:bg-gray-900 dark:border-gray-800">
        <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Last Week's Goals</h2>
        @foreach($prevWeekGoal->goals as $i => $goal)
            <div class="flex items-center gap-3 py-2 {{ !$loop->last ? 'border-b border-gray-50 dark:border-gray-800' : '' }}">
                <button wire:click="markPrevGoalMet({{ $i }}, {{ !($prevGoalMet[$i] ?? false) }})"
                    class="shrink-0">
                    @if($prevGoalMet[$i] ?? false)
                        <span class="flex items-center justify-center w-5 h-5 rounded-lg border-2 border-purple-500 bg-purple-500">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </span>
                    @else
                        <span class="w-5 h-5 rounded-lg border-2 border-gray-300 block dark:border-gray-600"></span>
                    @endif
                </button>
                <span class="text-sm text-gray-700 dark:text-gray-300 {{ $prevGoalMet[$i] ?? false ? 'line-through text-gray-400' : '' }}">{{ $goal }}</span>
            </div>
        @endforeach
    </div>
    @endif

    <div class="grid grid-cols-2 gap-6 mb-6">
        {{-- Category breakdown --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 dark:bg-gray-900 dark:border-gray-800">
            <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">By Category</h2>
            @forelse($categoryBreakdown as $item)
                <div class="flex items-center justify-between py-2.5 {{ !$loop->last ? 'border-b border-gray-50 dark:border-gray-800' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full" style="background-color: {{ $item['category']->color }}"></span>
                        <span class="text-sm text-gray-900 dark:text-gray-100">{{ $item['category']->name }}</span>
                    </div>
                    <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                        @if($item['steps_completed'] > 0)
                            <span>{{ $item['steps_completed'] }} steps</span>
                        @endif
                        @if($item['focus_minutes'] > 0)
                            <span>{{ $item['focus_minutes'] }}m focus</span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400 text-center py-4">No activity this week</p>
            @endforelse
        </div>

        {{-- Completed steps list --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 dark:bg-gray-900 dark:border-gray-800">
            <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Completed Steps</h2>
            <div class="max-h-64 overflow-y-auto">
                @forelse($stepsCompleted as $step)
                    <div class="flex items-center gap-2 py-1.5 {{ !$loop->last ? 'border-b border-gray-50 dark:border-gray-800' : '' }}">
                        <span class="flex items-center justify-center w-4 h-4 rounded border-2 border-purple-500 bg-purple-500 shrink-0">
                            <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span class="text-sm text-gray-600 dark:text-gray-300 truncate">{{ $step->title }}</span>
                        <span class="text-[10px] text-gray-400 shrink-0 ml-auto">{{ $step->completed_at->format('D') }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-4">No steps completed</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Goal setting & review prompts --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6 dark:bg-gray-900 dark:border-gray-800">
        <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Goals & Reflection</h2>
        <form wire:submit="saveGoals" class="space-y-4">
            {{-- Next week goals --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Goals for {{ $isCurrentWeek ? 'this' : 'that' }} week</label>
                <div class="space-y-2">
                    @for($i = 0; $i < 3; $i++)
                        <input wire:model="newGoals.{{ $i }}" type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" placeholder="Goal {{ $i + 1 }}">
                    @endfor
                </div>
            </div>

            {{-- Reflection prompts --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Biggest win</label>
                    <textarea wire:model="biggestWin" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" placeholder="What went well?"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Biggest blocker</label>
                    <textarea wire:model="biggestBlocker" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" placeholder="What held you back?"></textarea>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Priority for next week</label>
                <input wire:model="priorityNextWeek" type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" placeholder="The one thing that matters most">
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">Save Goals & Reflection</button>
                @if($saved)
                    <span class="text-sm text-green-600 dark:text-green-400 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Saved
                    </span>
                @endif
            </div>
        </form>
    </div>

    {{-- Daily logs --}}
    @if($dailyLogs->isNotEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 p-5 dark:bg-gray-900 dark:border-gray-800">
        <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Daily Logs</h2>
        @foreach($dailyLogs as $log)
            <div class="py-2.5 {{ !$loop->last ? 'border-b border-gray-50 dark:border-gray-800' : '' }}">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $log->date->format('l, M j') }}</span>
                    <div class="flex items-center gap-3">
                        @if($log->energy_level)
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400">E:{{ $log->energy_level }}</span>
                        @endif
                        @if($log->mood)
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">M:{{ $log->mood }}</span>
                        @endif
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="w-2 h-2 rounded-full {{ $i <= $log->focus_rating ? 'bg-purple-500' : 'bg-gray-200 dark:bg-gray-700' }}"></span>
                            @endfor
                        </div>
                    </div>
                </div>
                @if($log->daily_goal)
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Goal: {{ $log->daily_goal }}</p>
                @endif
                @if($log->notes)
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $log->notes }}</p>
                @endif
            </div>
        @endforeach
    </div>
    @endif
</div>
