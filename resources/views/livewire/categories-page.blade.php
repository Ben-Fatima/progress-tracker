<div>
    {{-- Header with New Category button at top --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Categories</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage your projects and track progress</p>
        </div>
        <button wire:click="$toggle('showCreateForm')" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Category
        </button>
    </div>

    {{-- Create form --}}
    @if($showCreateForm)
        <div class="bg-white rounded-2xl border border-gray-100 p-5 mb-6 dark:bg-gray-900 dark:border-gray-800">
            <form wire:submit="createCategory" class="space-y-3">
                <div class="flex gap-3">
                    <input wire:model="newCategoryName" type="text" class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" placeholder="Category name" required>
                    <input wire:model="newCategoryColor" type="color" class="w-10 h-10 rounded-lg border border-gray-200 cursor-pointer">
                </div>
                <input wire:model="newCategoryDescription" type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" placeholder="Description (optional)">
                <div class="flex gap-3 items-center">
                    <div class="flex items-center gap-2 flex-1">
                        <label class="text-xs text-gray-500 dark:text-gray-400 shrink-0">Deadline</label>
                        <input wire:model="newCategoryDeadline" type="date" class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
                    </div>
                    <div class="flex items-center gap-2 flex-1">
                        <label class="text-xs text-gray-500 dark:text-gray-400 shrink-0">Priority</label>
                        <select wire:model="newCategoryPriority" class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
                            @foreach(\App\Models\Category::PRIORITIES as $key => $p)
                                <option value="{{ $key }}">{{ $p['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition-colors">Create</button>
                    <button type="button" wire:click="$set('showCreateForm', false)" class="px-4 py-2 text-gray-600 dark:text-gray-400 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    @endif

    {{-- Parent Categories (grouped) --}}
    @if($parentCategories->isNotEmpty())
    @foreach($parentCategories as $parent)
    <div x-data="{ open: true }" class="mb-8">
        {{-- Parent header --}}
        <div class="bg-gradient-to-r from-gray-900 to-gray-800 dark:from-gray-800 dark:to-gray-900 rounded-2xl p-5 mb-4 cursor-pointer" @click="open = !open">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: {{ $parent->color }}20;">
                        <div class="w-3 h-3 rounded-full" style="background-color: {{ $parent->color }};"></div>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-bold text-white">{{ $parent->name }}</h2>
                            @if(($parent->priority ?? 'normal') !== 'normal')
                                @php $pConfig = $parent->priority_config; @endphp
                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $pConfig['bg'] }} {{ $pConfig['color'] }}">{{ $pConfig['icon'] }}</span>
                            @endif
                        </div>
                        @if($parent->description)
                            <p class="text-sm text-gray-400 mt-0.5">{{ $parent->description }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    @if($parent->deadline)
                        <span class="text-xs text-gray-400">{{ $parent->deadline->format('M j, Y') }}</span>
                    @endif
                    <div class="text-right">
                        <span class="text-2xl font-bold text-white">{{ $parent->children_progress }}%</span>
                        <p class="text-[10px] text-gray-500">overall</p>
                    </div>
                    <button @click.stop wire:click="deleteCategory({{ $parent->id }})" wire:confirm="Delete '{{ $parent->name }}' with all {{ $parent->children->count() }} sub-categories and their steps?" class="p-1.5 text-gray-500 hover:text-red-400 rounded-lg hover:bg-white/10 transition-colors" title="Delete this group">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                    <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
            {{-- Overall progress bar --}}
            <div class="mt-3 w-full bg-gray-700 rounded-full h-2">
                <div class="h-2 rounded-full transition-all duration-500" style="width: {{ $parent->children_progress }}%; background-color: {{ $parent->color }};"></div>
            </div>
            <div class="flex items-center gap-2 mt-2">
                <span class="text-[10px] text-gray-500">{{ $parent->children->count() }} sub-categories</span>
                @php
                    $childrenActive = $parent->children->filter(fn($c) => $c->archived_at === null);
                    $totalChildSteps = $childrenActive->sum(fn($c) => $c->total_steps);
                    $completedChildSteps = $childrenActive->sum(fn($c) => $c->completed_steps);
                @endphp
                <span class="text-[10px] text-gray-500">&middot; {{ $completedChildSteps }}/{{ $totalChildSteps }} steps</span>
            </div>
        </div>

        {{-- Children grid --}}
        <div x-show="open" x-collapse>
            <div class="grid grid-cols-2 gap-4 pl-4 border-l-2 ml-4" style="border-color: {{ $parent->color }}40;">
                @foreach($parent->children->sortBy(fn($c) => $c->progress === 100 ? 1 : 0) as $category)
                    <div class="bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-md hover:border-gray-200 transition-all dark:bg-gray-900 dark:border-gray-800 dark:hover:border-gray-700 {{ $category->progress === 100 ? 'opacity-60 hover:opacity-100' : '' }}">
                        <div class="flex items-start justify-between mb-3">
                            <a href="{{ route('categories.show', $category) }}" class="block flex-1">
                                <div class="flex items-center gap-2">
                                    @if($category->progress === 100)
                                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-sm">{{ $category->name }}</h3>
                                    @if(($category->priority ?? 'normal') !== 'normal')
                                        @php $pConfig = $category->priority_config; @endphp
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $pConfig['bg'] }} {{ $pConfig['color'] }}">{{ $pConfig['icon'] }}</span>
                                    @endif
                                </div>
                                @if($category->description)
                                    <p class="text-xs text-gray-500 mt-0.5 line-clamp-1 dark:text-gray-400">{{ $category->description }}</p>
                                @endif
                            </a>
                            @if($category->documents->isNotEmpty())
                                <div class="flex items-center gap-0.5 ml-2">
                                    @foreach($category->documents as $document)
                                        <a href="{{ route('documents.show', $document) }}" title="{{ $document->title }}" class="p-1.5 text-purple-500 hover:text-purple-700 hover:bg-purple-50 rounded-lg dark:hover:bg-purple-900/30">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                            <div class="flex gap-1 ml-2">
                                <button wire:click="startEditing({{ $category->id }})" class="p-1.5 text-gray-400 hover:text-purple-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <button wire:click="deleteCategory({{ $category->id }})" wire:confirm="Delete '{{ $category->name }}' and all its steps?" class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>

                        @if($editingCategoryId === $category->id)
                            <form wire:submit="updateCategory" class="space-y-3">
                                <div class="flex gap-3">
                                    <input wire:model="editName" type="text" class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" required>
                                    <input wire:model="editColor" type="color" class="w-10 h-10 rounded-lg border border-gray-200 cursor-pointer">
                                </div>
                                <input wire:model="editDescription" type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" placeholder="Description">
                                <div class="flex gap-3 items-center">
                                    <div class="flex items-center gap-2 flex-1">
                                        <label class="text-xs text-gray-500 dark:text-gray-400 shrink-0">Deadline</label>
                                        <input wire:model="editDeadline" type="date" class="flex-1 px-2 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
                                    </div>
                                    <div class="flex items-center gap-2 flex-1">
                                        <label class="text-xs text-gray-500 dark:text-gray-400 shrink-0">Priority</label>
                                        <select wire:model="editPriority" class="flex-1 px-2 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
                                            @foreach(\App\Models\Category::PRIORITIES as $key => $p)
                                                <option value="{{ $key }}">{{ $p['label'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit" class="px-3 py-1.5 bg-purple-600 text-white text-xs rounded-lg hover:bg-purple-700">Save</button>
                                    <button type="button" wire:click="$set('editingCategoryId', null)" class="px-3 py-1.5 text-gray-600 dark:text-gray-400 text-xs hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg">Cancel</button>
                                </div>
                            </form>
                        @else
                            <a href="{{ route('categories.show', $category) }}" class="block">
                                @if($category->deadline)
                                    <div class="flex items-center gap-1.5 mb-2">
                                        @if($category->isOverdue())
                                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-medium dark:bg-red-900/30 dark:text-red-400">Overdue by {{ abs($category->days_left) }}d</span>
                                        @elseif($category->isDueSoon())
                                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 font-medium dark:bg-amber-900/30 dark:text-amber-400">{{ $category->days_left }}d left</span>
                                        @else
                                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400">{{ $category->deadline->format('M j') }} ({{ $category->days_left }}d)</span>
                                        @endif
                                    </div>
                                @endif
                                @php
                                    $remainingMinutes = $category->steps()->where('status', '!=', 'done')->sum('estimated_minutes');
                                    $days = floor($remainingMinutes / 480);
                                    $hours = floor(($remainingMinutes % 480) / 60);
                                    $mins = $remainingMinutes % 60;
                                @endphp
                                <div class="flex items-center justify-between text-xs text-gray-500 mb-2 dark:text-gray-400">
                                    <span>{{ $category->completed_steps }} / {{ $category->total_steps }} steps</span>
                                    <span class="font-medium {{ $category->progress === 100 ? 'text-green-500' : '' }}">{{ $category->progress }}%</span>
                                </div>
                                @if($remainingMinutes > 0)
                                    <div class="flex items-center gap-1.5 mb-2">
                                        <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span class="text-xs font-medium text-purple-400">
                                            {{ $days > 0 ? "{$days}d " : '' }}{{ $hours > 0 ? "{$hours}h " : '' }}{{ $mins > 0 && $days === 0 ? "{$mins}m " : '' }}of work left
                                        </span>
                                    </div>
                                @endif
                                <div class="w-full bg-gray-100 rounded-full h-2 dark:bg-gray-800">
                                    <div class="h-2 rounded-full transition-all duration-500" style="width: {{ $category->progress }}%; background-color: {{ $category->color }}"></div>
                                </div>
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach
    @endif

    {{-- Standalone categories --}}
    @if($categories->isNotEmpty())
        <div class="grid grid-cols-2 gap-4 mb-8">
            @foreach($categories as $category)
                <div class="bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-md hover:border-gray-200 transition-all dark:bg-gray-900 dark:border-gray-800 dark:hover:border-gray-700">
                    @if($editingCategoryId === $category->id)
                        <form wire:submit="updateCategory" class="space-y-3">
                            <div class="flex gap-3">
                                <input wire:model="editName" type="text" class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" required>
                                <input wire:model="editColor" type="color" class="w-10 h-10 rounded-lg border border-gray-200 cursor-pointer">
                            </div>
                            <input wire:model="editDescription" type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" placeholder="Description">
                            <div class="flex gap-3 items-center">
                                <div class="flex items-center gap-2 flex-1">
                                    <label class="text-xs text-gray-500 dark:text-gray-400 shrink-0">Deadline</label>
                                    <input wire:model="editDeadline" type="date" class="flex-1 px-2 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
                                </div>
                                <div class="flex items-center gap-2 flex-1">
                                    <label class="text-xs text-gray-500 dark:text-gray-400 shrink-0">Priority</label>
                                    <select wire:model="editPriority" class="flex-1 px-2 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
                                        @foreach(\App\Models\Category::PRIORITIES as $key => $p)
                                            <option value="{{ $key }}">{{ $p['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="px-3 py-1.5 bg-purple-600 text-white text-xs rounded-lg hover:bg-purple-700">Save</button>
                                <button type="button" wire:click="$set('editingCategoryId', null)" class="px-3 py-1.5 text-gray-600 dark:text-gray-400 text-xs hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg">Cancel</button>
                            </div>
                        </form>
                    @else
                        <div class="flex items-start justify-between mb-3">
                            <a href="{{ route('categories.show', $category) }}" class="block flex-1">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $category->name }}</h3>
                                    @if(($category->priority ?? 'normal') !== 'normal')
                                        @php $pConfig = $category->priority_config; @endphp
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $pConfig['bg'] }} {{ $pConfig['color'] }}">{{ $pConfig['icon'] }}</span>
                                    @endif
                                </div>
                                @if($category->description)
                                    <p class="text-sm text-gray-500 mt-0.5 line-clamp-1 dark:text-gray-400">{{ $category->description }}</p>
                                @endif
                            </a>
                            @if($category->documents->isNotEmpty())
                                <div class="flex items-center gap-0.5 ml-2">
                                    @foreach($category->documents as $document)
                                        <a href="{{ route('documents.show', $document) }}" title="{{ $document->title }}" class="p-1.5 text-purple-500 hover:text-purple-700 hover:bg-purple-50 rounded-lg dark:hover:bg-purple-900/30">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                            <div class="flex gap-1 ml-2">
                                <button wire:click="startEditing({{ $category->id }})" class="p-1.5 text-gray-400 hover:text-purple-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <button wire:click="deleteCategory({{ $category->id }})" wire:confirm="Delete '{{ $category->name }}' and all its steps?" class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                        <a href="{{ route('categories.show', $category) }}" class="block">
                            @if($category->deadline)
                                <div class="flex items-center gap-1.5 mb-2">
                                    @if($category->isOverdue())
                                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-medium dark:bg-red-900/30 dark:text-red-400">
                                            Overdue by {{ abs($category->days_left) }}d
                                        </span>
                                    @elseif($category->isDueSoon())
                                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 font-medium dark:bg-amber-900/30 dark:text-amber-400">
                                            {{ $category->days_left }}d left
                                        </span>
                                    @else
                                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                                            {{ $category->deadline->format('M j') }} ({{ $category->days_left }}d)
                                        </span>
                                    @endif
                                </div>
                            @endif
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-2 dark:text-gray-400">
                                <span>{{ $category->completed_steps }} / {{ $category->total_steps }} steps</span>
                                <span class="font-medium">{{ $category->progress }}%</span>
                            </div>
                            @php
                                $remainingMinutes = $category->steps()->where('status', '!=', 'done')->sum('estimated_minutes');
                                $days = floor($remainingMinutes / 480);
                                $hours = floor(($remainingMinutes % 480) / 60);
                                $mins = $remainingMinutes % 60;
                            @endphp
                            @if($remainingMinutes > 0)
                                <div class="flex items-center gap-1.5 mb-2">
                                    <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-xs font-medium text-purple-400">
                                        {{ $days > 0 ? "{$days}d " : '' }}{{ $hours > 0 ? "{$hours}h " : '' }}{{ $mins > 0 && $days === 0 ? "{$mins}m " : '' }}of work left
                                    </span>
                                </div>
                            @endif
                            <div class="w-full bg-gray-100 rounded-full h-2 dark:bg-gray-800">
                                <div class="h-2 rounded-full transition-all duration-500" style="width: {{ $category->progress }}%; background-color: {{ $category->color }}"></div>
                            </div>
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- Completed Categories --}}
    @if($completedCategories->isNotEmpty())
    <div x-data="{ open: false }" class="mb-8">
        <button @click="open = !open" class="flex items-center gap-2 mb-4 group">
            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <h2 class="text-lg font-bold text-gray-400 dark:text-gray-500">Completed</h2>
            <span class="text-xs text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded-full">{{ $completedCategories->count() }}</span>
        </button>

        <div x-show="open" x-collapse>
            <div class="grid grid-cols-2 gap-4">
                @foreach($completedCategories as $category)
                    <div class="bg-white rounded-2xl border border-gray-100 p-5 opacity-60 hover:opacity-100 transition-all dark:bg-gray-900 dark:border-gray-800">
                        <div class="flex items-start justify-between mb-3">
                            <a href="{{ route('categories.show', $category) }}" class="block flex-1">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $category->name }}</h3>
                                </div>
                                @if($category->description)
                                    <p class="text-sm text-gray-500 mt-0.5 line-clamp-1 dark:text-gray-400">{{ $category->description }}</p>
                                @endif
                            </a>
                            @if($category->documents->isNotEmpty())
                                <div class="flex items-center gap-0.5 ml-2">
                                    @foreach($category->documents as $document)
                                        <a href="{{ route('documents.show', $document) }}" title="{{ $document->title }}" class="p-1.5 text-purple-500 hover:text-purple-700 hover:bg-purple-50 rounded-lg dark:hover:bg-purple-900/30">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                            <div class="flex gap-1 ml-2">
                                <button wire:click="deleteCategory({{ $category->id }})" wire:confirm="Delete '{{ $category->name }}' and all its steps?" class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                        <a href="{{ route('categories.show', $category) }}" class="block">
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-2 dark:text-gray-400">
                                <span>{{ $category->completed_steps }} / {{ $category->total_steps }} steps</span>
                                <span class="font-medium text-green-500">100%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2 dark:bg-gray-800">
                                <div class="h-2 rounded-full bg-green-500" style="width: 100%"></div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
