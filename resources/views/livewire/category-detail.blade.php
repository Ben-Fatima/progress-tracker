<div>
    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ $category->parent_id ? route('categories.show', $category->parent_id) : route('categories') }}" class="text-sm text-gray-500 hover:text-purple-600 dark:text-gray-400 dark:hover:text-purple-400 mb-4 inline-block transition-colors">&larr; Back to {{ $category->parent_id ? $category->parent->name : 'Categories' }}</a>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold">{{ $category->name }}</h1>
                @if(($category->priority ?? 'normal') !== 'normal')
                    @php $pConfig = $category->priority_config; @endphp
                    <span class="text-xs font-bold px-2 py-0.5 rounded {{ $pConfig['bg'] }} {{ $pConfig['color'] }}">{{ $pConfig['label'] }}</span>
                @endif
                @if($category->documents->isNotEmpty())
                    <div class="flex items-center gap-1 ml-2">
                        @foreach($category->documents as $document)
                            <a href="{{ route('documents.show', $document) }}" title="{{ $document->title }}" class="inline-flex items-center gap-1 px-2 py-1 text-xs text-purple-600 hover:text-purple-700 hover:bg-purple-50 rounded-lg dark:text-purple-400 dark:hover:bg-purple-900/30">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Docs</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        @if($category->description)
            <p class="text-gray-500 mt-1 dark:text-gray-400">{{ $category->description }}</p>
        @endif
        @if($category->deadline)
            <div class="flex items-center gap-2 mt-2">
                @if($category->isOverdue())
                    <span class="text-xs px-2.5 py-1 rounded-full bg-red-100 text-red-700 font-medium dark:bg-red-900/30 dark:text-red-400">
                        Overdue by {{ abs($category->days_left) }} days — deadline was {{ $category->deadline->format('M j, Y') }}
                    </span>
                @elseif($category->isDueSoon())
                    <span class="text-xs px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 font-medium dark:bg-amber-900/30 dark:text-amber-400">
                        {{ $category->days_left }} days left — due {{ $category->deadline->format('M j, Y') }}
                    </span>
                @else
                    <span class="text-xs px-2.5 py-1 rounded-full bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                        Due {{ $category->deadline->format('M j, Y') }} ({{ $category->days_left }}d)
                    </span>
                @endif
            </div>
        @endif
        <div class="mt-4">
            <div class="flex items-center justify-between text-sm text-gray-500 mb-2 dark:text-gray-400">
                <span>{{ $category->completed_steps }} / {{ $category->total_steps }} steps complete</span>
                <span class="font-medium">{{ $category->progress }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2.5 dark:bg-gray-800">
                <div class="h-2.5 rounded-full transition-all duration-500" style="width: {{ $category->progress }}%; background-color: {{ $category->color }}"></div>
            </div>
        </div>
    </div>

    {{-- Sub-categories --}}
    @if($subCategories->isNotEmpty())
        <div class="mb-6">
            <h2 class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-3 dark:text-gray-500">{{ $subCategories->count() }} sub-categories</h2>
            <div class="grid gap-3 sm:grid-cols-2">
                @foreach($subCategories as $sub)
                    <a href="{{ route('categories.show', $sub) }}" class="block bg-white rounded-xl border border-gray-100 p-4 hover:shadow-md hover:border-gray-200 transition-all dark:bg-gray-900 dark:border-gray-800 dark:hover:border-gray-700">
                        <div class="flex items-start gap-2">
                            <span class="mt-1.5 w-2 h-2 rounded-full shrink-0" style="background-color: {{ $sub->color }}"></span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-100">{{ $sub->name }}</p>
                                <div class="flex items-center gap-2 mt-2">
                                    <div class="flex-1 bg-gray-100 rounded-full h-1.5 dark:bg-gray-800">
                                        <div class="h-1.5 rounded-full transition-all duration-500" style="width: {{ $sub->progress }}%; background-color: {{ $sub->color }}"></div>
                                    </div>
                                    <span class="text-[10px] text-gray-400 shrink-0">{{ $sub->completed_steps }}/{{ $sub->total_steps }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Filters --}}
    <div class="flex gap-1 mb-6 bg-gray-100 dark:bg-gray-800 p-1 rounded-xl w-fit">
        @foreach(['all' => 'All', 'todo' => 'Todo', 'done' => 'Done'] as $key => $label)
            <button wire:click="$set('filter', '{{ $key }}')"
                class="px-4 py-1.5 rounded-lg text-sm font-medium transition-all {{ $filter === $key ? 'bg-white text-purple-700 shadow-sm dark:bg-gray-700 dark:text-purple-300' : 'text-gray-500 dark:text-gray-400' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Step list --}}
    <div class="space-y-1.5">
        @forelse($steps as $step)
            <div class="bg-white rounded-xl border {{ $step->isOverdue() ? 'border-red-200 bg-red-50/30 dark:bg-red-900/10' : ($step->isDueSoon() ? 'border-amber-200 dark:border-amber-800' : 'border-gray-100 dark:border-gray-800 dark:bg-gray-900') }} transition-all hover:shadow-sm">
                {{-- Parent step --}}
                <div class="flex items-center gap-3 px-4 py-3">
                    <button wire:click="toggleStatus({{ $step->id }})" class="shrink-0">
                        @if($step->status === 'done')
                            <span class="flex items-center justify-center w-5 h-5 rounded-lg border-2 border-purple-500 bg-purple-500">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                        @elseif($step->status === 'in_progress')
                            <span class="flex items-center justify-center w-5 h-5 rounded-lg border-2 border-purple-500 bg-purple-50 dark:bg-purple-900/30">
                                <span class="w-2 h-2 rounded-sm bg-purple-500"></span>
                            </span>
                        @else
                            <span class="w-5 h-5 rounded-lg border-2 border-gray-200 block dark:border-gray-700"></span>
                        @endif
                    </button>

                    <div class="flex-1 min-w-0">
                        @if($editingStepId === $step->id)
                            <form wire:submit="updateStep" class="space-y-2">
                                <input wire:model="editStepTitle" type="text" class="w-full px-2 py-1 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" required>
                                <textarea wire:model="editStepNotes" rows="2" class="w-full px-2 py-1 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" placeholder="Notes (optional)"></textarea>
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-2">
                                        <label class="text-xs text-gray-500 dark:text-gray-400">Due:</label>
                                        <input wire:model="editStepDueDate" type="date" class="px-2 py-1 border border-gray-200 rounded-lg text-xs dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <label class="text-xs text-gray-500 dark:text-gray-400">Est (min):</label>
                                        <input wire:model="editStepEstimation" type="number" min="1" max="600" class="w-20 px-2 py-1 border border-gray-200 rounded-lg text-xs dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" placeholder="e.g. 30">
                                    </div>
                                    <button type="submit" class="text-purple-600 dark:text-purple-400 text-sm font-medium">Save</button>
                                    <button type="button" wire:click="$set('editingStepId', null)" class="text-gray-400 text-sm">Cancel</button>
                                </div>
                            </form>
                        @else
                            <div class="flex items-center gap-2">
                                <button wire:click="toggleExpand({{ $step->id }})" class="text-left flex-1 min-w-0">
                                    <span class="{{ $step->status === 'done' ? 'line-through text-gray-400' : 'text-gray-900 dark:text-gray-100' }} text-sm">{{ $step->title }}</span>
                                </button>
                                @if($step->notes)
                                    <span class="text-gray-300 shrink-0 dark:text-gray-600" title="Has notes">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM6 20V4h5v7h7v9H6z"/></svg>
                                    </span>
                                @endif
                                @if($step->document_id)
                                    <a href="{{ route('documents.show', $step->document_id) }}" class="text-purple-400 hover:text-purple-600 shrink-0 dark:text-purple-500 dark:hover:text-purple-300" title="{{ $step->document?->title ?? 'Open document' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </a>
                                @endif
                                @if($step->due_date)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded shrink-0
                                        {{ $step->isOverdue() ? 'bg-red-100 text-red-700 font-medium dark:bg-red-900/30 dark:text-red-400' : ($step->isDueSoon() ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : ($step->status === 'done' ? 'bg-gray-100 text-gray-400 dark:bg-gray-800' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400')) }}">
                                        {{ $step->isOverdue() ? 'Overdue' : '' }}
                                        {{ $step->due_date->format('M j') }}
                                    </span>
                                @endif
                                @if($step->estimated_minutes)
                                    @php $actual = (int) ($step->actual_minutes ?? 0); @endphp
                                    <span class="text-[10px] px-1.5 py-0.5 rounded shrink-0 bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400" title="Estimated time">
                                        Est: {{ $step->estimated_minutes >= 60 ? floor($step->estimated_minutes / 60) . 'h' . ($step->estimated_minutes % 60 > 0 ? ' ' . ($step->estimated_minutes % 60) . 'm' : '') : $step->estimated_minutes . 'm' }}
                                    </span>
                                    @if($actual > 0)
                                        <span class="text-[10px] px-1.5 py-0.5 rounded shrink-0 bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-400" title="Actual time from focus sessions">
                                            Actual: {{ \App\Models\FocusSession::formatDuration($actual * 60) }}
                                        </span>
                                    @endif
                                @endif
                            </div>
                        @endif
                    </div>

                    @if($editingStepId !== $step->id)
                    <div class="flex items-center gap-1 shrink-0">
                        @if($step->status !== 'done')
                            @if($focusedStepId === $step->id)
                                <a href="/focus" class="px-2 py-1 text-xs bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 rounded-lg flex items-center gap-1 animate-pulse" title="Currently in focus session">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/></svg>
                                    Focusing
                                </a>
                            @else
                                <button wire:click="startFocus({{ $step->id }})" class="px-2 py-1 text-xs text-purple-600 dark:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/30 rounded-lg transition-colors flex items-center gap-1" title="Start focus session">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    Focus
                                </button>
                            @endif
                        @endif
                        <button wire:click="startEditingStep({{ $step->id }})" class="p-1 text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 rounded-lg transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                        <button wire:click="$set('addingSubStepTo', {{ $step->id }})" class="p-1 text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 rounded-lg transition-colors" title="Add sub-step">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </button>
                        <button wire:click="deleteStep({{ $step->id }})" wire:confirm="Delete this step?" class="p-1 text-gray-400 hover:text-red-600 rounded-lg transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                    @endif
                </div>

                {{-- Expanded notes panel --}}
                @if($expandedStepId === $step->id && $editingStepId !== $step->id)
                    <div class="border-t border-gray-100 dark:border-gray-800 px-4 py-3 bg-gray-50/50 dark:bg-gray-800/50">
                        <div x-data="{ notes: @js($step->notes ?? '') }" class="space-y-2">
                            <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Notes</label>
                            <textarea
                                x-model="notes"
                                wire:model.blur="editStepNotes"
                                x-init="$watch('notes', () => $wire.set('editStepNotes', notes))"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200"
                                placeholder="Add notes, links, context..."
                            ></textarea>
                            <div class="flex justify-end">
                                <button wire:click="saveNotes({{ $step->id }})" class="px-3 py-1 bg-purple-600 text-white text-xs rounded-lg hover:bg-purple-700 transition-colors">Save Notes</button>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Children --}}
                @if($step->children->isNotEmpty())
                    <div class="border-t border-gray-100 dark:border-gray-800 ml-8 mr-4 pb-2">
                        @foreach($step->children as $child)
                            <div class="flex items-center gap-3 px-4 py-2">
                                <button wire:click="toggleStatus({{ $child->id }})" class="shrink-0">
                                    @if($child->status === 'done')
                                        <span class="flex items-center justify-center w-4 h-4 rounded border-2 border-purple-500 bg-purple-500">
                                            <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </span>
                                    @else
                                        <span class="w-4 h-4 rounded border-2 border-gray-200 block dark:border-gray-700"></span>
                                    @endif
                                </button>
                                <div class="flex-1 min-w-0">
                                    @if($editingStepId === $child->id)
                                        <form wire:submit="updateStep" class="space-y-2">
                                            <input wire:model="editStepTitle" type="text" class="w-full px-2 py-1 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" required>
                                            <textarea wire:model="editStepNotes" rows="2" class="w-full px-2 py-1 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" placeholder="Notes (optional)"></textarea>
                                            <div class="flex items-center gap-3">
                                                <div class="flex items-center gap-2">
                                                    <label class="text-xs text-gray-500 dark:text-gray-400">Due:</label>
                                                    <input wire:model="editStepDueDate" type="date" class="px-2 py-1 border border-gray-200 rounded-lg text-xs dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <label class="text-xs text-gray-500 dark:text-gray-400">Est (min):</label>
                                                    <input wire:model="editStepEstimation" type="number" min="1" max="600" class="w-20 px-2 py-1 border border-gray-200 rounded-lg text-xs dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" placeholder="e.g. 30">
                                                </div>
                                                <button type="submit" class="text-purple-600 dark:text-purple-400 text-xs font-medium">Save</button>
                                                <button type="button" wire:click="$set('editingStepId', null)" class="text-gray-400 text-xs">Cancel</button>
                                            </div>
                                        </form>
                                    @else
                                        <div class="flex items-center gap-2">
                                            <span class="{{ $child->status === 'done' ? 'line-through text-gray-400' : 'text-gray-700 dark:text-gray-300' }} text-sm">{{ $child->title }}</span>
                                            @if($child->due_date)
                                                <span class="text-[10px] px-1.5 py-0.5 rounded shrink-0
                                                    {{ $child->isOverdue() ? 'bg-red-100 text-red-700 font-medium dark:bg-red-900/30 dark:text-red-400' : ($child->isDueSoon() ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : ($child->status === 'done' ? 'bg-gray-100 text-gray-400 dark:bg-gray-800' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400')) }}">
                                                    {{ $child->isOverdue() ? 'Overdue' : '' }}
                                                    {{ $child->due_date->format('M j') }}
                                                </span>
                                            @endif
                                            @if($child->estimated_minutes)
                                                @php $actual = (int) ($child->actual_minutes ?? 0); @endphp
                                                <span class="text-[10px] px-1.5 py-0.5 rounded shrink-0 bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400" title="Estimated time">
                                                    Est: {{ $child->estimated_minutes >= 60 ? floor($child->estimated_minutes / 60) . 'h' . ($child->estimated_minutes % 60 > 0 ? ' ' . ($child->estimated_minutes % 60) . 'm' : '') : $child->estimated_minutes . 'm' }}
                                                </span>
                                                @if($actual > 0)
                                                    <span class="text-[10px] px-1.5 py-0.5 rounded shrink-0 bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-400" title="Actual time from focus sessions">
                                                        Actual: {{ \App\Models\FocusSession::formatDuration($actual * 60) }}
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                @if($editingStepId !== $child->id)
                                <div class="flex items-center gap-1 shrink-0">
                                    <button wire:click="startEditingStep({{ $child->id }})" class="p-1 text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 rounded-lg transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                    <button wire:click="deleteStep({{ $child->id }})" wire:confirm="Delete this sub-step?" class="p-1 text-gray-400 hover:text-red-600 rounded-lg transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Add sub-step form --}}
                @if($addingSubStepTo === $step->id)
                    <div class="border-t border-gray-100 dark:border-gray-800 ml-8 mr-4 px-4 py-2">
                        <form wire:submit="addSubStep({{ $step->id }})" class="flex gap-2">
                            <input wire:model="newSubStepTitle" type="text" class="flex-1 px-2 py-1 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" placeholder="Sub-step title" required autofocus>
                            <button type="submit" class="text-purple-600 dark:text-purple-400 text-sm font-medium">Add</button>
                            <button type="button" wire:click="$set('addingSubStepTo', null)" class="text-gray-400 text-sm">Cancel</button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-12 text-gray-400 dark:text-gray-500">
                <p>@if($subCategories->isNotEmpty())No steps directly on this category — open a sub-category above.@else No steps yet. Add your first step below. @endif</p>
            </div>
        @endforelse
    </div>

    {{-- Add step form --}}
    <div class="mt-4">
        <form wire:submit="addStep" class="flex gap-2">
            <input wire:model="newStepTitle" type="text" class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-900 dark:border-gray-800 dark:text-gray-200" placeholder="Add a new step...">
            <button type="submit" class="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">Add Step</button>
        </form>
    </div>
</div>
