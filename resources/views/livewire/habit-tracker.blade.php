<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Habits</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                @if($totalHabits > 0)
                    {{ $completedToday }}/{{ $totalHabits }} done today
                @else
                    Track your daily habits
                @endif
            </p>
        </div>
        <button wire:click="$toggle('showForm')" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Habit
        </button>
    </div>

    {{-- Day summary --}}
    <div class="bg-white rounded-2xl border border-gray-100 px-5 py-3 mb-6 dark:bg-gray-900 dark:border-gray-800">
        <span class="text-xs text-gray-400 uppercase tracking-wide">Day summary</span>
        <p class="text-sm text-gray-900 dark:text-gray-100 mt-1">{{ $dayCompletions }} completions on {{ $summaryDate }}</p>
    </div>

    {{-- Create/edit form --}}
    @if($showForm)
    <div class="bg-white rounded-2xl border border-gray-100 p-5 mb-6 dark:bg-gray-900 dark:border-gray-800">
        <form wire:submit="{{ $editingId ? 'updateHabit' : 'createHabit' }}" class="space-y-3">
            <div class="flex gap-3">
                <input wire:model="habitIcon" type="text" class="w-16 px-3 py-2 border border-gray-200 rounded-lg text-sm text-center focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" placeholder="Icon" maxlength="4">
                <input wire:model="habitName" type="text" class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" placeholder="Habit name" required>
                <select wire:model="habitFrequency" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition-colors">{{ $editingId ? 'Update' : 'Create' }}</button>
                <button type="button" wire:click="resetForm" class="px-4 py-2 text-gray-600 dark:text-gray-400 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors">Cancel</button>
            </div>
        </form>
    </div>
    @endif

    {{-- Habit grid --}}
    @if($habits->isNotEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden dark:bg-gray-900 dark:border-gray-800">
        {{-- Date headers --}}
        <div class="grid items-center border-b border-gray-100 dark:border-gray-800 px-5 py-3" style="grid-template-columns: 1fr 40px repeat(7, 44px)">
            <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Habit</span>
            <span class="text-xs font-medium text-gray-400 text-center">Streak</span>
            @foreach($dates as $date)
                <span class="text-xs text-center {{ $date === today()->toDateString() ? 'font-bold text-purple-600 dark:text-purple-400' : 'text-gray-400' }}">
                    {{ \Carbon\Carbon::parse($date)->format('D') }}<br>
                    <span class="text-[10px]">{{ \Carbon\Carbon::parse($date)->format('d') }}</span>
                </span>
            @endforeach
        </div>

        {{-- Habit rows --}}
        @foreach($habits as $habit)
            <div class="grid items-center border-b border-gray-50 dark:border-gray-800/50 px-5 py-3 hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors" style="grid-template-columns: 1fr 40px repeat(7, 44px)">
                <div class="flex items-center gap-2 min-w-0">
                    @if($habit->icon)
                        <span class="text-lg shrink-0">{{ $habit->icon }}</span>
                    @endif
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $habit->name }}</span>
                    <div class="flex gap-0.5 shrink-0 opacity-0 group-hover:opacity-100">
                        <button wire:click="editHabit({{ $habit->id }})" class="p-1 text-gray-300 hover:text-purple-600 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                        <button wire:click="deleteHabit({{ $habit->id }})" wire:confirm="Delete '{{ $habit->name }}'?" class="p-1 text-gray-300 hover:text-red-600 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
                <div class="text-center">
                    @if($streaks[$habit->id] > 0)
                        <span class="text-xs font-bold text-purple-600 dark:text-purple-400">{{ $streaks[$habit->id] }}</span>
                    @else
                        <span class="text-xs text-gray-300 dark:text-gray-600">0</span>
                    @endif
                </div>
                @foreach($dates as $date)
                    @php $isCompleted = $logs->has($habit->id . '-' . $date); @endphp
                    <div class="flex justify-center">
                        <button wire:click="toggleHabit({{ $habit->id }}, '{{ $date }}')"
                            class="w-8 h-8 rounded-lg flex items-center justify-center transition-all
                            {{ $isCompleted
                                ? 'bg-purple-500 text-white shadow-sm'
                                : ($date === today()->toDateString()
                                    ? 'border-2 border-dashed border-purple-300 dark:border-purple-700 hover:border-purple-500 hover:bg-purple-50 dark:hover:bg-purple-900/20'
                                    : 'border border-gray-200 dark:border-gray-700 hover:border-gray-300') }}">
                            @if($isCompleted)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </button>
                    </div>
                @endforeach
            </div>
        @endforeach

        {{-- Today's completion bar --}}
        @if($totalHabits > 0)
        <div class="px-5 py-3 bg-gray-50/50 dark:bg-gray-800/30">
            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                <span>Today's progress</span>
                <span class="font-medium">{{ $completedToday }}/{{ $totalHabits }}</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div class="h-2 bg-purple-500 rounded-full transition-all duration-500" style="width: {{ $totalHabits > 0 ? round(($completedToday / $totalHabits) * 100) : 0 }}%"></div>
            </div>
        </div>
        @endif
    </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center dark:bg-gray-900 dark:border-gray-800">
            <p class="text-gray-400 dark:text-gray-500 mb-2">No habits yet</p>
            <p class="text-sm text-gray-400 dark:text-gray-500">Add daily habits to build consistency</p>
        </div>
    @endif
</div>
