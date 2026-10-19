<div>
    {{-- Header: Greeting + Momentum + Level --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                @php $hour = now()->hour; @endphp
                @if($hour < 12) Good morning @elseif($hour < 18) Good afternoon @else Good evening @endif
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ now()->format('l, M j') }}</p>
        </div>
        <div class="flex items-center gap-6">
            {{-- Momentum --}}
            <div class="text-right">
                <div class="flex items-center gap-2">
                    <span class="text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100">{{ $momentum['score'] }}</span>
                    <span class="text-sm text-gray-400">/100</span>
                    @if($momentum['trend'] === 'rising')
                        <span class="text-green-500 text-xs font-bold">↑</span>
                    @elseif($momentum['trend'] === 'declining')
                        <span class="text-red-500 text-xs font-bold">↓</span>
                    @endif
                </div>
                <p class="text-[10px] text-gray-400 uppercase tracking-wide">Momentum</p>
            </div>
            {{-- Level --}}
            <div class="text-center">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-600 to-purple-800 flex items-center justify-center">
                    <span class="text-white font-bold text-sm">{{ $momentum['level']['level'] }}</span>
                </div>
                <p class="text-[10px] text-gray-400 mt-1">LVL</p>
            </div>
        </div>
    </div>

    {{-- Daily Fact --}}
    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-100 rounded-2xl p-5 mb-6 dark:from-indigo-900/10 dark:to-purple-900/10 dark:border-indigo-800/40">
        <div class="flex items-start gap-3">
            <span class="text-2xl shrink-0">💡</span>
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <p class="text-xs font-bold text-indigo-700 dark:text-indigo-400 uppercase tracking-wide">Did you know?</p>
                    <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">{{ $dailyFact['category'] }}</span>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $dailyFact['text'] }}</p>
            </div>
        </div>
    </div>

    {{-- Capacity Score --}}
    @if($capacity['has_data'])
    @php
        $capState = $capacity['state'];
        $capScore = $capacity['score'];
        $capColorMap = [
            'red' => 'from-red-500 to-red-700',
            'yellow' => 'from-yellow-500 to-orange-600',
            'blue' => 'from-blue-500 to-cyan-600',
            'purple' => 'from-purple-500 to-indigo-600',
            'amber' => 'from-amber-400 to-yellow-500',
        ];
        $capGradient = $capColorMap[$capState['color']] ?? 'from-gray-500 to-gray-700';
        $capBorderMap = [
            'red' => 'border-red-500/30',
            'yellow' => 'border-yellow-500/30',
            'blue' => 'border-blue-500/30',
            'purple' => 'border-purple-500/30',
            'amber' => 'border-amber-400/30',
        ];
        $capBorder = $capBorderMap[$capState['color']] ?? 'border-gray-500/30';
    @endphp
    <div class="bg-white rounded-2xl border {{ $capBorder }} p-5 mb-6 dark:bg-gray-900">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                {{-- Score circle --}}
                <div class="w-14 h-14 rounded-full bg-gradient-to-br {{ $capGradient }} flex items-center justify-center shadow-lg">
                    <span class="text-white font-bold text-lg">{{ $capScore }}</span>
                </div>
                {{-- State info --}}
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xl">{{ $capState['emoji'] }}</span>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $capState['label'] }} Mode</h3>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $capState['advice'] }}</p>
                </div>
            </div>
            <div class="text-right flex flex-col items-end gap-1.5">
                <p class="text-[10px] uppercase tracking-wide text-gray-400 mb-1">Capacity</p>
                <div class="w-24 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r {{ $capGradient }} rounded-full transition-all" style="width: {{ $capScore }}%"></div>
                </div>
                <button wire:click="$toggle('showCapacityAdjust')" class="text-[10px] text-gray-400 hover:text-purple-500 transition-colors">
                    Adjust
                </button>
            </div>
        </div>

        {{-- Adjust capacity panel --}}
        @if($showCapacityAdjust)
        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">How do you actually feel?</p>
            <div class="flex items-center gap-2 flex-wrap">
                @foreach([
                    ['label' => 'Zombie', 'emoji' => "\u{1F9DF}", 'color' => 'red'],
                    ['label' => 'Human', 'emoji' => "\u{1F610}", 'color' => 'yellow'],
                    ['label' => 'Charged', 'emoji' => "\u{26A1}", 'color' => 'blue'],
                    ['label' => 'Beast', 'emoji' => "\u{1F525}", 'color' => 'purple'],
                    ['label' => 'Legendary', 'emoji' => "\u{1F451}", 'color' => 'amber'],
                ] as $opt)
                    <button wire:click="setCapacityOverride('{{ $opt['label'] }}')"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-all
                        {{ ($capacityOverride ?? '') === $opt['label']
                            ? 'bg-purple-100 text-purple-700 ring-2 ring-purple-500 dark:bg-purple-900/40 dark:text-purple-300'
                            : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700' }}">
                        <span>{{ $opt['emoji'] }}</span> {{ $opt['label'] }}
                    </button>
                @endforeach
                @if($capacityOverride)
                    <button wire:click="clearCapacityOverride" class="text-[10px] text-gray-400 hover:text-red-500 ml-1">Reset to auto</button>
                @endif
            </div>
        </div>
        @endif

        @if(count($capacity['factors']) > 0)
        <div class="flex items-center gap-3 mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
            @foreach($capacity['factors'] as $factor)
                <span class="text-[11px] px-2 py-1 rounded-md bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                    {{ $factor['label'] }}: <span class="font-medium text-gray-800 dark:text-gray-200">{{ $factor['value'] }}</span>
                </span>
            @endforeach
            @if($capacity['is_override'] ?? false)
                <span class="text-[10px] px-1.5 py-0.5 rounded bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">Manual</span>
            @endif
        </div>
        @endif
    </div>
    @endif

    {{-- Energy Mode Picker (if not set today) --}}
    @if(!$energyMode)
    <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6 dark:bg-gray-900 dark:border-gray-800">
        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">How's your energy today?</p>
        <div class="grid grid-cols-4 gap-3">
            <button wire:click="setEnergyMode('low')" class="flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-gray-100 hover:border-purple-300 hover:bg-purple-50/50 transition-all dark:border-gray-800 dark:hover:border-purple-700 dark:hover:bg-purple-900/10">
                <span class="text-2xl">😴</span>
                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Low</span>
                <span class="text-[10px] text-gray-400">~15 min</span>
            </button>
            <button wire:click="setEnergyMode('normal')" class="flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-gray-100 hover:border-purple-300 hover:bg-purple-50/50 transition-all dark:border-gray-800 dark:hover:border-purple-700 dark:hover:bg-purple-900/10">
                <span class="text-2xl">😐</span>
                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Normal</span>
                <span class="text-[10px] text-gray-400">~30 min</span>
            </button>
            <button wire:click="setEnergyMode('high')" class="flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-gray-100 hover:border-purple-300 hover:bg-purple-50/50 transition-all dark:border-gray-800 dark:hover:border-purple-700 dark:hover:bg-purple-900/10">
                <span class="text-2xl">⚡</span>
                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">High</span>
                <span class="text-[10px] text-gray-400">~45 min</span>
            </button>
            <button wire:click="setEnergyMode('peak')" class="flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-gray-100 hover:border-purple-300 hover:bg-purple-50/50 transition-all dark:border-gray-800 dark:hover:border-purple-700 dark:hover:bg-purple-900/10">
                <span class="text-2xl">🔥</span>
                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Peak</span>
                <span class="text-[10px] text-gray-400">~60 min</span>
            </button>
        </div>
    </div>
    @else
    {{-- Energy mode indicator (already set) --}}
    <div class="flex items-center gap-3 mb-6">
        <div class="flex items-center gap-2 px-3 py-1.5 bg-purple-50 rounded-lg dark:bg-purple-900/20">
            <span>{{ match($energyMode) { 'low' => '😴', 'normal' => '😐', 'high' => '⚡', 'peak' => '🔥', default => '😐' } }}</span>
            <span class="text-xs font-medium text-purple-700 dark:text-purple-300 capitalize">{{ $energyMode }} energy</span>
        </div>
        <button wire:click="setEnergyMode('low')" class="text-[10px] text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 transition-colors">Change</button>
    </div>
    @endif

    {{-- TODAY'S MISSION --}}
    @if($mission)
    <div class="mb-8">
        @if($mission['is_minimum_day'])
            {{-- Minimum Viable Day --}}
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-6 dark:from-amber-900/10 dark:to-orange-900/10 dark:border-amber-800">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-lg">🛟</span>
                    <p class="text-xs font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wide">Minimum Viable Day</p>
                </div>
                <p class="text-sm text-amber-800 dark:text-amber-300 mb-4">Low energy day. You don't need to win — just maintain momentum.</p>

                <div class="bg-white/60 dark:bg-gray-900/40 rounded-xl p-5">
                    <p class="font-bold text-lg text-gray-900 dark:text-gray-100 mb-1">{{ $mission['step']->title }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">{{ $mission['category']->name }}</p>
                    <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                        <span>~{{ $mission['estimated_minutes'] }} min</span>
                        <span>+{{ $mission['xp'] }} XP</span>
                    </div>
                </div>

                <a href="{{ route('focus') }}" class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Start Mission
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        @else
            {{-- Normal Mission --}}
            <div class="bg-gradient-to-br from-purple-600 to-purple-800 rounded-2xl p-6 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-48 h-48 bg-white/5 rounded-full -mr-24 -mt-24"></div>
                <div class="relative">
                    <p class="text-purple-300 text-xs font-bold uppercase tracking-wide mb-4">Today's Mission</p>

                    <p class="font-bold text-2xl mb-1 leading-tight">{{ $mission['step']->title }}</p>
                    <p class="text-purple-300 text-sm mb-4">{{ $mission['category']->name }}</p>

                    {{-- Progress bar for category --}}
                    @php $progress = $mission['category']->progress ?? 0; @endphp
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex-1 bg-white/20 rounded-full h-2">
                            <div class="bg-white rounded-full h-2 transition-all" style="width: {{ $progress }}%"></div>
                        </div>
                        <span class="text-xs text-purple-200 font-medium">{{ $progress }}%</span>
                    </div>

                    {{-- Why this? --}}
                    @if(!empty($mission['reasons']))
                    <div class="mb-4 space-y-1">
                        @foreach($mission['reasons'] as $reason)
                            <p class="text-xs text-purple-200 flex items-center gap-1.5">
                                <span class="text-purple-400">→</span> {{ $reason }}
                            </p>
                        @endforeach
                    </div>
                    @endif

                    {{-- Meta --}}
                    <div class="flex items-center gap-4 mb-5 text-xs text-purple-200">
                        <span>~{{ $mission['estimated_minutes'] }} min</span>
                        <span>+{{ $mission['xp'] }} XP</span>
                    </div>

                    <a href="{{ route('focus') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/20 hover:bg-white/30 text-white text-sm font-medium rounded-lg transition-colors backdrop-blur-sm">
                        Start Mission
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        @endif
    </div>
    @endif

    {{-- Stats row --}}
    <div class="grid grid-cols-4 gap-4 mb-8">
        {{-- Momentum breakdown --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 dark:bg-gray-900 dark:border-gray-800">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide mb-3">Momentum</p>
            <div class="space-y-2">
                @foreach($momentum['factors'] as $factor)
                    <div>
                        <div class="flex items-center justify-between text-[11px] mb-0.5">
                            <span class="text-gray-500 dark:text-gray-400">{{ $factor['label'] }}</span>
                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $factor['score'] }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5 dark:bg-gray-800">
                            <div class="h-1.5 rounded-full bg-purple-500 transition-all" style="width: {{ $factor['score'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Streak --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 dark:bg-gray-900 dark:border-gray-800">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center dark:bg-purple-900/30">
                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.24 17 7.343 18.657 11 18.657 14 17.657 18.657z"/></svg>
                </div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Streak</p>
            </div>
            <p class="text-3xl font-bold tracking-tight">{{ $currentStreak }}<span class="text-base font-normal text-gray-400 ml-1">days</span></p>
            <p class="text-xs text-gray-400 mt-1">Best: {{ $bestStreak }} days</p>
        </div>

        {{-- Focus Today --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 dark:bg-gray-900 dark:border-gray-800">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center dark:bg-purple-900/30">
                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Focus Today</p>
            </div>
            @php $focusMin = max(0, $todayStats['focus_minutes']); @endphp
            <p class="text-3xl font-bold tracking-tight">{{ intdiv($focusMin, 60) }}h <span class="text-2xl">{{ $focusMin % 60 }}m</span></p>
            <p class="text-xs text-gray-400 mt-1">+{{ $todayXP }} XP today</p>
        </div>

        {{-- Deadlines --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 dark:bg-gray-900 dark:border-gray-800">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $overdueSteps->count() > 0 ? 'bg-red-100 dark:bg-red-900/30' : 'bg-gray-100 dark:bg-gray-800' }}">
                    <svg class="w-4 h-4 {{ $overdueSteps->count() > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Deadlines</p>
            </div>
            @if($overdueSteps->isNotEmpty() || $dueSoonSteps->isNotEmpty())
                <div class="space-y-1.5 max-h-24 overflow-y-auto">
                    @foreach($overdueSteps->take(3) as $step)
                        <a href="{{ route('categories.show', $step->category_id) }}" class="flex items-center gap-1.5 group">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 shrink-0"></span>
                            <span class="text-[11px] text-red-700 dark:text-red-400 truncate group-hover:underline">{{ $step->title }}</span>
                        </a>
                    @endforeach
                    @foreach($dueSoonSteps->take(2) as $step)
                        <a href="{{ route('categories.show', $step->category_id) }}" class="flex items-center gap-1.5 group">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                            <span class="text-[11px] text-gray-700 dark:text-gray-300 truncate group-hover:underline">{{ $step->title }}</span>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-3xl font-bold tracking-tight text-gray-200 dark:text-gray-700">0</p>
                <p class="text-xs text-gray-400 mt-1">All clear</p>
            @endif
        </div>
    </div>

    {{-- Focus Stats Widget --}}
    @if($focusStats['has_data'])
    <div class="bg-white rounded-2xl border border-gray-100 p-5 mb-8 dark:bg-gray-900 dark:border-gray-800">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <span class="text-lg">🎯</span>
                <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Focus This Week</h2>
            </div>
            <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                <span>{{ $focusStats['week_sessions'] }} sessions</span>
                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $focusStats['week_duration_label'] }} total</span>
                @if($focusStats['trend'] === 'up')
                    <span class="text-green-500 font-bold">↑</span>
                @elseif($focusStats['trend'] === 'down')
                    <span class="text-red-500 font-bold">↓</span>
                @endif
            </div>
        </div>

        {{-- Bar chart --}}
        <div class="flex items-end gap-2 h-24 mb-3">
            @foreach($focusStats['daily'] as $day)
                <div class="flex-1 flex flex-col items-center gap-1">
                    @if($day['minutes'] > 0)
                        <span class="text-[9px] text-gray-500 dark:text-gray-400 font-medium">{{ \App\Models\FocusSession::formatDuration($day['seconds']) }}</span>
                    @endif
                    <div class="w-full rounded-t-md transition-all {{ $day['seconds'] > 0 ? 'bg-purple-500' : 'bg-gray-100 dark:bg-gray-800' }}"
                         style="height: {{ $day['seconds'] > 0 ? max(4, $day['chart_percentage'] * 0.8) : 4 }}px">
                    </div>
                    <span class="text-[10px] text-gray-400">{{ $day['date'] }}</span>
                </div>
            @endforeach
        </div>

        {{-- Stats row --}}
        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 pt-3 border-t border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-4">
                <span>Avg session: <span class="font-medium text-gray-700 dark:text-gray-300">{{ $focusStats['avg_duration_label'] }}</span></span>
                <span>Longest: <span class="font-medium text-gray-700 dark:text-gray-300">{{ $focusStats['longest_session_label'] }}</span></span>
            </div>
            <a href="{{ route('focus') }}" class="text-purple-600 dark:text-purple-400 hover:underline">Start focus →</a>
        </div>
    </div>
    @endif

    {{-- Sleep Schedule Widget --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5 mb-8 dark:bg-gray-900 dark:border-gray-800">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <span class="text-lg">🛏️</span>
                <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Sleep Schedule</h2>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-[10px] text-gray-400">Target: {{ $sleep['target_sleep'] }} → {{ $sleep['target_wake'] }}</span>
                @if($sleep['today_score'] !== null)
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $sleep['today_score'] >= 75 ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : ($sleep['today_score'] >= 50 ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400') }}">{{ $sleep['today_score'] }}%</span>
                @endif
                <button wire:click="$toggle('showSleepSettings')" class="text-[10px] text-purple-600 dark:text-purple-400 hover:underline">Edit target</button>
            </div>
        </div>

        @if($showSleepSettings)
            <div class="mb-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                <div class="flex items-end gap-3">
                    <div>
                        <label class="block text-[10px] font-medium text-gray-500 dark:text-gray-400 mb-1">Sleep at</label>
                        <input wire:model="targetSleep" type="time" class="px-3 py-2 border border-gray-200 rounded-lg text-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
                    </div>
                    <div>
                        <label class="block text-[10px] font-medium text-gray-500 dark:text-gray-400 mb-1">Wake at</label>
                        <input wire:model="targetWake" type="time" class="px-3 py-2 border border-gray-200 rounded-lg text-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
                    </div>
                    <button wire:click="saveSleepTargets" class="px-3 py-2 bg-purple-600 text-white text-xs font-medium rounded-lg hover:bg-purple-700">Save</button>
                </div>
                @error('targetSleep') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                @error('targetWake') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
            </div>
        @endif

        {{-- Timeline chart: 21:00 to 11:00 --}}
        <div class="relative mb-3">
            {{-- Y-axis time labels --}}
            <div class="flex justify-between text-[8px] text-gray-400 mb-1 px-8">
                <span>21:00</span>
                <span>23:00</span>
                <span>01:00</span>
                <span>03:00</span>
                <span>05:00</span>
                <span>07:00</span>
                <span>09:00</span>
                <span>11:00</span>
            </div>

            {{-- Chart area --}}
            <div class="relative h-40 bg-gray-50 dark:bg-gray-800/50 rounded-lg overflow-hidden">
                {{-- Target zone band (23:00 to 07:00) --}}
                <div class="absolute bg-green-100/50 dark:bg-green-900/15 border-l border-r border-dashed border-green-400/40 dark:border-green-600/30"
                     style="left: {{ $sleep['target_sleep_pos'] }}%; right: {{ 100 - $sleep['target_wake_pos'] }}%; top: 0; bottom: 0;">
                </div>

                {{-- Target sleep line (23:00) --}}
                <div class="absolute top-0 bottom-0 border-l-2 border-dashed border-green-500/50" style="left: {{ $sleep['target_sleep_pos'] }}%">
                    <span class="absolute -top-0 left-1 text-[8px] text-green-600 dark:text-green-400 font-medium">sleep</span>
                </div>

                {{-- Target wake line (07:00) --}}
                <div class="absolute top-0 bottom-0 border-l-2 border-dashed border-green-500/50" style="left: {{ $sleep['target_wake_pos'] }}%">
                    <span class="absolute -top-0 left-1 text-[8px] text-green-600 dark:text-green-400 font-medium">wake</span>
                </div>

                {{-- Day rows --}}
                @foreach($sleep['days'] as $index => $day)
                    @php
                        $rowHeight = 100 / max(count($sleep['days']), 1);
                        $top = $index * $rowHeight;
                        $score = $day['score'] ?? 0;
                        $barColor = $score >= 75 ? 'bg-purple-500/80' : ($score >= 50 ? 'bg-amber-500/80' : ($score > 0 ? 'bg-red-500/80' : ''));
                    @endphp
                    <div class="absolute flex items-center" style="top: {{ $top }}%; height: {{ $rowHeight }}%; left: 0; right: 0;">
                        {{-- Day label --}}
                        <span class="absolute left-1 text-[9px] font-medium text-gray-500 dark:text-gray-400 z-10">{{ $day['date'] }}</span>

                        {{-- Sleep bar --}}
                        @if($day['sleep_pos'] !== null && $day['wake_pos'] !== null)
                            @php
                                $sPos = $day['sleep_pos'];
                                $wPos = $day['wake_pos'];
                                $tSleep = $sleep['target_sleep_pos'];
                                $tWake = $sleep['target_wake_pos'];
                                // Overlap with target zone
                                $overlapStart = max($sPos, $tSleep);
                                $overlapEnd = min($wPos, $tWake);
                                $hasOverlap = $overlapStart < $overlapEnd;
                            @endphp
                            {{-- Full bar (outside target = red) --}}
                            <div class="absolute bg-red-500/70 rounded-sm"
                                 style="left: {{ $sPos }}%; width: {{ $wPos - $sPos }}%; top: 20%; height: 60%;">
                            </div>
                            {{-- Overlap portion (inside target = green) --}}
                            @if($hasOverlap)
                                <div class="absolute bg-green-500/80 rounded-sm"
                                     style="left: {{ $overlapStart }}%; width: {{ $overlapEnd - $overlapStart }}%; top: 20%; height: 60%;">
                                </div>
                            @endif
                            {{-- Time label --}}
                            <div class="absolute flex items-center justify-center"
                                 style="left: {{ $sPos }}%; width: {{ $wPos - $sPos }}%; top: 20%; height: 60%;">
                                <span class="text-[8px] text-white font-bold truncate px-1 drop-shadow-sm">
                                    {{ \Carbon\Carbon::parse($day['slept_at'])->format('H:i') }}–{{ \Carbon\Carbon::parse($day['woke_at'])->format('H:i') }}
                                </span>
                            </div>
                        @endif

                        {{-- Separator line --}}
                        @if(!$loop->last)
                            <div class="absolute bottom-0 left-0 right-0 border-b border-gray-200/30 dark:border-gray-700/30"></div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Summary stats --}}
        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 pt-3 border-t border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-4">
                @if($sleep['avg_score'] !== null)
                    <span>Avg score: <span class="font-medium text-gray-700 dark:text-gray-300">{{ $sleep['avg_score'] }}%</span></span>
                @endif
                @php
                    $validDays = array_filter($sleep['days'], fn($d) => $d['sleep_hours'] !== null);
                    $avgHours = !empty($validDays) ? round(array_sum(array_column($validDays, 'sleep_hours')) / count($validDays), 1) : null;
                @endphp
                @if($avgHours !== null)
                    <span>Avg sleep: <span class="font-medium text-gray-700 dark:text-gray-300">{{ $avgHours }}h</span></span>
                @endif
            </div>
            <a href="{{ route('log') }}" class="text-purple-600 dark:text-purple-400 hover:underline">Log today →</a>
        </div>
    </div>

    {{-- Categories Summary --}}
    @if($topLevelCategories->isNotEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 p-5 mb-8 dark:bg-gray-900 dark:border-gray-800">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Categories</h2>
            </div>
            <a href="{{ route('categories') }}" class="text-xs text-purple-600 dark:text-purple-400 hover:underline font-medium">View all &rarr;</a>
        </div>
        <div class="space-y-3">
            @foreach($topLevelCategories->take(5) as $cat)
                @php
                    $progress = $cat->children->isNotEmpty() ? $cat->children_progress : $cat->progress;
                @endphp
                <a href="{{ $cat->children->isNotEmpty() ? route('categories') : route('categories.show', $cat) }}" class="flex items-center gap-3 group">
                    <div class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $cat->color }};"></div>
                    <span class="text-sm text-gray-700 dark:text-gray-300 flex-1 truncate group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">{{ $cat->name }}</span>
                    @if(($cat->priority ?? 'normal') !== 'normal')
                        @php $pConfig = $cat->priority_config; @endphp
                        <span class="text-[9px] font-bold px-1 py-0.5 rounded {{ $pConfig['bg'] }} {{ $pConfig['color'] }}">{{ $pConfig['icon'] }}</span>
                    @endif
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 shrink-0">{{ $progress }}%</span>
                    <div class="w-20 bg-gray-100 rounded-full h-1.5 dark:bg-gray-800 shrink-0">
                        <div class="h-1.5 rounded-full transition-all" style="width: {{ $progress }}%; background-color: {{ $cat->color }};"></div>
                    </div>
                </a>
            @endforeach
        </div>
        @if($topLevelCategories->count() > 5)
            <p class="text-[10px] text-gray-400 mt-3">+ {{ $topLevelCategories->count() - 5 }} more</p>
        @endif
    </div>
    @endif

    {{-- Recent Activity --}}
    @if($recentActivity->isNotEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 p-5 dark:bg-gray-900 dark:border-gray-800">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4 dark:text-gray-400">Recent Activity</h2>
        <div class="space-y-3">
            @foreach($recentActivity as $activity)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0
                        {{ $activity->type === 'step_completed' ? 'bg-green-100 dark:bg-green-900/30' : ($activity->type === 'session_completed' ? 'bg-purple-100 dark:bg-purple-900/30' : 'bg-gray-100 dark:bg-gray-800') }}">
                        @if($activity->type === 'step_completed')
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @elseif($activity->type === 'session_completed')
                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @else
                            <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-900 dark:text-gray-100 truncate">{{ $activity->description }}</p>
                        <p class="text-[10px] text-gray-400">{{ $activity->created_at->diffForHumans() }} @if($activity->category) &middot; {{ $activity->category->name }} @endif</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
