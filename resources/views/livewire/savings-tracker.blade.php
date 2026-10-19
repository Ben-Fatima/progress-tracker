<div>
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold">Savings Tracker</h1>
        <button wire:click="$set('showGoalForm', true)" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Goal
        </button>
    </div>

    {{-- Overview stats --}}
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-2xl border border-gray-100 p-5 dark:bg-gray-900 dark:border-gray-800">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center dark:bg-purple-900/30">
                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                </div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Saved</p>
            </div>
            <p class="text-3xl font-bold tracking-tight">{{ number_format($totalSaved, 2) }} <span class="text-base font-normal text-gray-400">MAD</span></p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5 dark:bg-gray-900 dark:border-gray-800">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center dark:bg-purple-900/30">
                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Target</p>
            </div>
            <p class="text-3xl font-bold tracking-tight">{{ number_format($totalTarget, 2) }} <span class="text-base font-normal text-gray-400">MAD</span></p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5 dark:bg-gray-900 dark:border-gray-800">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center dark:bg-purple-900/30">
                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Overall Progress</p>
            </div>
            @php $overallPct = $totalTarget > 0 ? min(100, round(($totalSaved / $totalTarget) * 100, 1)) : 0; @endphp
            <p class="text-3xl font-bold tracking-tight">{{ $overallPct }}%</p>
        </div>
    </div>

    {{-- Goal form --}}
    @if($showGoalForm)
    <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6 dark:bg-gray-900 dark:border-gray-800">
        <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ $editingGoalId ? 'Edit Goal' : 'New Savings Goal' }}</h2>
        <form wire:submit="{{ $editingGoalId ? 'updateGoal' : 'createGoal' }}" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                    <input wire:model="goalTitle" type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" placeholder="e.g. Emergency Fund" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Target Amount (MAD)</label>
                    <input wire:model="targetAmount" type="number" step="0.01" min="0.01" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" placeholder="5000" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Target Date (optional)</label>
                    <input wire:model="targetDate" type="date" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color</label>
                    <div class="flex items-center gap-2">
                        <input wire:model="goalColor" type="color" class="w-10 h-10 rounded-lg border border-gray-200 cursor-pointer">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Pick a color for this goal</span>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description (optional)</label>
                <input wire:model="goalDescription" type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" placeholder="What is this savings for?">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">{{ $editingGoalId ? 'Update' : 'Create' }}</button>
                <button type="button" wire:click="resetGoalForm" class="px-4 py-2 text-gray-600 dark:text-gray-400 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors">Cancel</button>
            </div>
        </form>
    </div>
    @endif

    {{-- Goal cards --}}
    @if($goals->isNotEmpty())
    <div class="space-y-4 mb-6">
        @foreach($goals as $goal)
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden transition-all dark:bg-gray-900 dark:border-gray-800 {{ $activeGoalId === $goal->id ? 'ring-2 ring-purple-500' : 'hover:shadow-md' }}">
                {{-- Goal header (clickable) --}}
                <div wire:click="selectGoal({{ $goal->id }})" class="w-full text-left p-5 cursor-pointer">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: {{ $goal->color }}20">
                                <svg class="w-5 h-5" style="color: {{ $goal->color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $goal->title }}</h3>
                                @if($goal->description)
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $goal->description }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2" wire:click.stop>
                            <button wire:click="editGoal({{ $goal->id }})" class="p-1.5 text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </button>
                            <button wire:click="deleteGoal({{ $goal->id }})" wire:confirm="Delete this savings goal and all its transactions?" class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Progress bar --}}
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="font-bold text-gray-900 dark:text-gray-100">{{ number_format($goal->current_balance, 2) }} <span class="text-sm font-normal text-gray-400">MAD</span></span>
                        <span class="text-gray-500 dark:text-gray-400">of {{ number_format($goal->target_amount, 2) }} MAD</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3 dark:bg-gray-800">
                        <div class="h-3 rounded-full transition-all duration-500 flex items-center justify-end" style="width: {{ $goal->progress_percentage }}%; background-color: {{ $goal->color }}">
                            @if($goal->progress_percentage >= 10)
                                <span class="text-[10px] text-white font-medium pr-2">{{ $goal->progress_percentage }}%</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-xs text-gray-400">{{ $goal->progress_percentage }}% complete</span>
                        <div class="flex items-center gap-3">
                            @if($goal->target_date)
                                <span class="text-xs text-gray-400">Target: {{ $goal->target_date->format('M j, Y') }}</span>
                            @endif
                            @if($goal->is_completed)
                                <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 font-medium">Goal Reached!</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Expanded view: transactions --}}
                @if($activeGoalId === $goal->id)
                <div class="border-t border-gray-100 dark:border-gray-800">
                    {{-- Add transaction form --}}
                    <div class="p-5 bg-gray-50/50 dark:bg-gray-800/50">
                        <form wire:submit="addTransaction" class="grid grid-cols-12 gap-3 items-end">
                            <div class="col-span-2">
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Type</label>
                                <div class="flex gap-1 bg-gray-100 dark:bg-gray-700 p-1 rounded-lg">
                                    <button type="button" wire:click="$set('transactionType', 'deposit')"
                                        class="flex-1 py-1.5 text-xs font-medium rounded-md transition-all {{ $transactionType === 'deposit' ? 'bg-white text-green-600 shadow-sm dark:bg-gray-800 dark:text-green-400' : 'text-gray-500 dark:text-gray-400' }}">
                                        + Add
                                    </button>
                                    <button type="button" wire:click="$set('transactionType', 'withdrawal')"
                                        class="flex-1 py-1.5 text-xs font-medium rounded-md transition-all {{ $transactionType === 'withdrawal' ? 'bg-white text-red-600 shadow-sm dark:bg-gray-800 dark:text-red-400' : 'text-gray-500 dark:text-gray-400' }}">
                                        &minus; Take
                                    </button>
                                </div>
                            </div>
                            <div class="col-span-3">
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Amount (MAD)</label>
                                <input wire:model="transactionAmount" type="number" step="0.01" min="0.01" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" placeholder="100" required>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Date</label>
                                <input wire:model="transactionDate" type="date" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" required>
                            </div>
                            <div class="col-span-3">
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Note (optional)</label>
                                <input wire:model="transactionNote" type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" placeholder="Monthly deposit">
                            </div>
                            <div class="col-span-2">
                                <button type="submit" class="w-full px-3 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
                                    {{ $transactionType === 'deposit' ? 'Add Money' : 'Take Money' }}
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Transaction list --}}
                    <div class="p-5">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3 dark:text-gray-400">Transaction History</h4>
                        @if($activeGoal && $activeGoal->transactions->isNotEmpty())
                            <div class="space-y-1 max-h-64 overflow-y-auto">
                                @foreach($activeGoal->transactions as $tx)
                                    <div class="flex items-center gap-3 py-2 px-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0
                                            {{ $tx->type === 'deposit' ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30' }}">
                                            @if($tx->type === 'deposit')
                                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            @else
                                                <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-900 dark:text-gray-100">{{ $tx->note ?? ($tx->type === 'deposit' ? 'Deposit' : 'Withdrawal') }}</p>
                                            <p class="text-[10px] text-gray-400">{{ $tx->date->format('M j, Y') }}</p>
                                        </div>
                                        <span class="text-sm font-bold {{ $tx->type === 'deposit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $tx->type === 'deposit' ? '+' : '-' }}{{ number_format(abs((float)$tx->amount), 2) }} MAD
                                        </span>
                                        <button wire:click="deleteTransaction({{ $tx->id }})" wire:confirm="Delete this transaction?" class="p-1 text-gray-300 hover:text-red-600 dark:text-gray-600 dark:hover:text-red-400 rounded transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-400 text-center py-4">No transactions yet. Add money to start tracking.</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-16">
        <div class="w-16 h-16 rounded-2xl bg-purple-100 flex items-center justify-center mx-auto mb-4 dark:bg-purple-900/30">
            <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
        </div>
        <p class="text-gray-500 dark:text-gray-400 mb-1">No savings goals yet</p>
        <p class="text-sm text-gray-400 mb-4">Create your first goal to start tracking your savings progress.</p>
        <button wire:click="$set('showGoalForm', true)" class="inline-flex items-center gap-1.5 px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Goal
        </button>
    </div>
    @endif
</div>
