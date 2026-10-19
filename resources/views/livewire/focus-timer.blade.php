<div>
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold">Focus Timer</h1>
        <button wire:click="$toggle('showSettings')" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Settings
        </button>
    </div>

    {{-- Settings panel --}}
    @if($showSettings)
    <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6 dark:bg-gray-900 dark:border-gray-800">
        <h3 class="font-semibold text-gray-900 mb-4 dark:text-gray-100">Pomodoro Settings</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-xs text-gray-500 dark:text-gray-400">Work (min)</label>
                <input wire:model="workMinutes" type="number" min="1" max="120" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-gray-400">Short break (min)</label>
                <input wire:model="breakMinutes" type="number" min="1" max="60" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-gray-400">Long break (min)</label>
                <input wire:model="longBreakMinutes" type="number" min="1" max="120" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-gray-400">Blocks before long break</label>
                <input wire:model="blocksBeforeLongBreak" type="number" min="2" max="10" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
            </div>
        </div>
        <div class="flex gap-2 mt-4">
            <button wire:click="saveSettings" class="px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition-colors">Save</button>
            <button wire:click="$set('showSettings', false)" class="px-4 py-2 text-gray-600 dark:text-gray-400 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors">Cancel</button>
        </div>
    </div>
    @endif

    {{-- Post-session feedback modal --}}
    @if($showFeedback)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md dark:bg-gray-900">
            <h3 class="font-bold text-lg text-gray-900 dark:text-gray-100 mb-2">How did it go?</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">Quick feedback to improve your missions.</p>

            {{-- Outcome --}}
            <div class="mb-5">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-3 uppercase tracking-wide">Outcome</p>
                <div class="grid grid-cols-2 gap-2">
                    <button wire:click="$set('feedbackOutcome', 'completed')" class="p-3 rounded-xl border-2 transition-all text-center {{ $feedbackOutcome === 'completed' ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-100 dark:border-gray-800 hover:border-gray-200' }}">
                        <span class="text-lg block mb-1">✅</span>
                        <span class="text-xs text-gray-600 dark:text-gray-400">Completed</span>
                    </button>
                    <button wire:click="$set('feedbackOutcome', 'progress')" class="p-3 rounded-xl border-2 transition-all text-center {{ $feedbackOutcome === 'progress' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-100 dark:border-gray-800 hover:border-gray-200' }}">
                        <span class="text-lg block mb-1">📈</span>
                        <span class="text-xs text-gray-600 dark:text-gray-400">Made progress</span>
                    </button>
                    <button wire:click="$set('feedbackOutcome', 'blocked')" class="p-3 rounded-xl border-2 transition-all text-center {{ $feedbackOutcome === 'blocked' ? 'border-orange-500 bg-orange-50 dark:bg-orange-900/20' : 'border-gray-100 dark:border-gray-800 hover:border-gray-200' }}">
                        <span class="text-lg block mb-1">🚧</span>
                        <span class="text-xs text-gray-600 dark:text-gray-400">Got blocked</span>
                    </button>
                    <button wire:click="$set('feedbackOutcome', 'distracted')" class="p-3 rounded-xl border-2 transition-all text-center {{ $feedbackOutcome === 'distracted' ? 'border-red-500 bg-red-50 dark:bg-red-900/20' : 'border-gray-100 dark:border-gray-800 hover:border-gray-200' }}">
                        <span class="text-lg block mb-1">😵‍💫</span>
                        <span class="text-xs text-gray-600 dark:text-gray-400">Got distracted</span>
                    </button>
                </div>
            </div>

            {{-- Difficulty --}}
            <div class="mb-6">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-3 uppercase tracking-wide">Difficulty</p>
                <div class="flex gap-2 justify-center">
                    @for($i = 1; $i <= 5; $i++)
                        <button wire:click="$set('feedbackDifficulty', {{ $i }})" class="w-10 h-10 rounded-full border-2 flex items-center justify-center transition-all {{ $feedbackDifficulty >= $i ? 'border-purple-500 bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300' : 'border-gray-200 text-gray-300 dark:border-gray-700' }}">
                            <span class="text-sm font-bold">{{ $i }}</span>
                        </button>
                    @endfor
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3">
                <button wire:click="submitFeedback" class="flex-1 px-4 py-2.5 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">Save</button>
                <button wire:click="skipFeedback" class="px-4 py-2.5 text-gray-500 text-sm hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">Skip</button>
            </div>
        </div>
    </div>
    @endif

    <div>
        {{-- Mission context (if coming from dashboard) --}}
        @if($mission && !$activeSession)
        <div class="bg-purple-50 dark:bg-purple-900/10 border border-purple-100 dark:border-purple-800 rounded-2xl p-4 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-purple-600 dark:text-purple-400 uppercase tracking-wide mb-1">Current Mission</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $mission['step']->title }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $mission['category']->name }} &middot; ~{{ $mission['estimated_minutes'] }} min &middot; +{{ $mission['xp'] }} XP</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Timer card --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-8 text-center mb-8 dark:bg-gray-900 dark:border-gray-800">
            {{-- Mode toggle --}}
            <div class="flex justify-center gap-1 mb-6 bg-gray-100 dark:bg-gray-800 p-1 rounded-xl w-fit mx-auto">
                <button wire:click="setMode('free')"
                    class="px-5 py-2 rounded-lg text-sm font-medium transition-all {{ $mode === 'free' ? 'bg-white text-purple-700 shadow-sm dark:bg-gray-700 dark:text-purple-300' : 'text-gray-500 dark:text-gray-400' }} {{ $activeSession ? 'opacity-50 cursor-not-allowed' : '' }}">
                    Free
                </button>
                <button wire:click="setMode('pomodoro')"
                    class="px-5 py-2 rounded-lg text-sm font-medium transition-all {{ $mode === 'pomodoro' ? 'bg-white text-purple-700 shadow-sm dark:bg-gray-700 dark:text-purple-300' : 'text-gray-500 dark:text-gray-400' }} {{ $activeSession ? 'opacity-50 cursor-not-allowed' : '' }}">
                    Pomodoro
                </button>
            </div>

            {{-- Category select --}}
            <div class="mb-4">
                <label class="block text-sm text-gray-500 dark:text-gray-400 mb-2">Working on</label>
                <select wire:model.live="selectedCategoryId" class="mx-auto px-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" {{ $activeSession ? 'disabled' : '' }}>
                    <option value="">General</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Step select (mission-centric) --}}
            @if($availableSteps->isNotEmpty() && !$activeSession)
            <div class="mb-6">
                <label class="block text-sm text-gray-500 dark:text-gray-400 mb-2">Step</label>
                <select wire:model="selectedStepId" class="mx-auto px-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
                    <option value="">None (general focus)</option>
                    @foreach($availableSteps as $step)
                        <option value="{{ $step->id }}">{{ $step->title }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Timer display --}}
            <div class="mb-8"
                 x-data="{
                     mode: @js($mode),
                     running: @js((bool)$activeSession),
                     paused: @js((bool)$activeSession?->paused_at),
                     startedAt: @js($activeSession?->started_at?->timestamp),
                     pausedSeconds: @js($activeSession?->paused_seconds ?? 0),
                     pausedAt: @js($activeSession?->paused_at?->timestamp),
                     onBreak: @js($onBreak),
                     workMinutes: @js($workMinutes),
                     breakMinutes: @js($this->getCurrentBreakMinutes()),
                     pomodoroBlock: @js($pomodoroBlock),
                     elapsed: 0,
                     remaining: 0,
                     interval: null,
                     notified: false,
                     init() {
                         if (this.interval) { clearInterval(this.interval); this.interval = null; }
                         if (this.running && this.startedAt) {
                             this.updateElapsed();
                             if (!this.paused) this.startTimer();
                         } else {
                             this.elapsed = 0;
                         }
                     },
                     updateElapsed() {
                         let now = Math.floor(Date.now() / 1000);
                         let totalElapsed = now - this.startedAt;
                         let totalPaused = this.pausedSeconds;
                         if (this.paused && this.pausedAt) {
                             totalPaused += now - this.pausedAt;
                         }
                         this.elapsed = Math.max(0, totalElapsed - totalPaused);
                     },
                     startTimer() {
                         if (this.interval) clearInterval(this.interval);
                         this.interval = setInterval(() => {
                             if (!this.running || this.paused) { clearInterval(this.interval); this.interval = null; return; }
                             this.updateElapsed();
                             if (this.mode === 'pomodoro') {
                                 let totalSeconds = this.onBreak ? this.breakMinutes * 60 : this.workMinutes * 60;
                                 this.remaining = Math.max(0, totalSeconds - this.elapsed);
                                 if (this.remaining <= 0 && !this.notified) {
                                     this.notified = true;
                                     this.notify();
                                 }
                             }
                         }, 1000);
                     },
                     notify() {
                         if ('Notification' in window && Notification.permission === 'granted') {
                             new Notification(this.onBreak ? 'Break over! Time to work.' : 'Work block done! Take a break.');
                         }
                         try {
                             let ctx = new (window.AudioContext || window.webkitAudioContext)();
                             let osc = ctx.createOscillator();
                             osc.connect(ctx.destination);
                             osc.frequency.value = 800;
                             osc.start(); osc.stop(ctx.currentTime + 0.3);
                         } catch(e) {}
                     },
                     get displayTime() {
                         if (!this.running) return '00:00:00';
                         if (this.mode === 'pomodoro') {
                             let totalSeconds = this.onBreak ? this.breakMinutes * 60 : this.workMinutes * 60;
                             let rem = Math.max(0, totalSeconds - this.elapsed);
                             let m = Math.floor(rem / 60);
                             let s = rem % 60;
                             return this.pad(m) + ':' + this.pad(s);
                         }
                         let h = Math.floor(this.elapsed / 3600);
                         let m = Math.floor((this.elapsed % 3600) / 60);
                         let s = this.elapsed % 60;
                         return this.pad(h) + ':' + this.pad(m) + ':' + this.pad(s);
                     },
                     pad(n) { return String(n).padStart(2, '0'); }
                 }"
                 x-init="init()"
            >
                <p class="text-6xl font-mono font-bold tabular-nums" :class="paused ? 'animate-pulse opacity-60' : ''" x-text="displayTime">00:00:00</p>

                @if($activeSession && $activeSession->isPaused())
                    <p class="text-sm text-amber-500 mt-2 font-medium">Paused</p>
                @elseif($activeSession && $mode === 'pomodoro')
                    <div class="mt-3">
                        @if($onBreak)
                            <span class="inline-block px-3 py-1 bg-green-100 text-green-700 text-sm rounded-full font-medium dark:bg-green-900/30 dark:text-green-400">Break time</span>
                        @else
                            <span class="text-sm text-gray-500 dark:text-gray-400">Block {{ $pomodoroBlock }} / {{ $blocksBeforeLongBreak }}</span>
                        @endif
                    </div>
                @elseif($activeSession)
                    <p class="text-sm text-gray-400 mt-2">Focus session in progress</p>
                @endif
            </div>

            {{-- Controls --}}
            <div class="flex justify-center gap-3">
                @if($activeSession)
                    @if($mode === 'pomodoro' && $onBreak)
                        <button wire:click="skipBreak" class="px-6 py-3 bg-purple-600 text-white font-medium rounded-xl hover:bg-purple-700 transition-colors">Skip Break</button>
                    @endif
                    @if($activeSession->isPaused())
                        <button wire:click="resumeSession" class="px-6 py-3 bg-purple-600 text-white font-medium rounded-xl hover:bg-purple-700 transition-colors">
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                Resume
                            </span>
                        </button>
                    @else
                        <button wire:click="pauseSession" class="px-6 py-3 bg-amber-500 text-white font-medium rounded-xl hover:bg-amber-600 transition-colors">
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                                Pause
                            </span>
                        </button>
                    @endif
                    <button wire:click="stopSession" class="px-6 py-3 bg-green-600 text-white font-medium rounded-xl hover:bg-green-700 transition-colors">Stop & Save</button>
                    <button wire:click="cancelSession" wire:confirm="Cancel this session? It won't be saved." class="px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-colors dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">Cancel</button>
                @else
                    <button wire:click="startSession" class="px-8 py-3 bg-purple-600 text-white font-medium rounded-xl hover:bg-purple-700 transition-colors text-lg">
                        Start {{ $mode === 'pomodoro' ? 'Pomodoro' : 'Focus' }}
                    </button>
                @endif
            </div>

            @if(!$activeSession && $mode === 'pomodoro')
                <p class="text-xs text-gray-400 mt-4">{{ $workMinutes }}min work / {{ $breakMinutes }}min break / {{ $longBreakMinutes }}min long break every {{ $blocksBeforeLongBreak }} blocks</p>
            @endif
        </div>

        {{-- Today's stats --}}
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-white rounded-2xl border border-gray-100 p-5 text-center dark:bg-gray-900 dark:border-gray-800">
                <p class="text-2xl font-bold tracking-tight">{{ \App\Models\FocusSession::formatDuration($totalSeconds) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total focus today</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5 text-center dark:bg-gray-900 dark:border-gray-800">
                <p class="text-2xl font-bold tracking-tight">{{ $todayPomodoroCount }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pomodoros completed</p>
            </div>
        </div>

        {{-- Today's sessions --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 dark:bg-gray-900 dark:border-gray-800">
            <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Today's Sessions</h2>

            @forelse($todaySessions as $session)
                <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-gray-50 dark:border-gray-800' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full {{ $session->type === 'pomodoro' ? 'bg-purple-500' : 'bg-green-500' }}"></span>
                        <span class="text-sm text-gray-900 dark:text-gray-100">{{ $session->category?->name ?? 'General' }}</span>
                        <span class="text-xs text-gray-400">{{ $session->started_at->format('H:i') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs px-1.5 py-0.5 rounded {{ $session->type === 'pomodoro' ? 'bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400' : 'bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-400' }}">{{ $session->type }}</span>
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ $session->durationLabel() }}</span>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400 text-center py-4">No sessions yet today</p>
            @endforelse
        </div>
    </div>
</div>
