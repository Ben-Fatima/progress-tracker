<div class="max-w-5xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold dark:text-gray-100">Documents</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Plans, notes, references, and checklists linked to your categories</p>
        </div>
        <button wire:click="$set('showForm', true)"
                class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition-colors">
            + New Document
        </button>
    </div>

    {{-- Filters --}}
    <div class="flex items-center gap-3">
        <select wire:model.live="filterType"
                class="px-3 py-1.5 text-sm border border-gray-200 rounded-lg bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300">
            <option value="">All types</option>
            @foreach($types as $key => $config)
                <option value="{{ $key }}">{{ $config['label'] }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterCategory"
                class="px-3 py-1.5 text-sm border border-gray-200 rounded-lg bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300">
            <option value="">All categories</option>
            @foreach($allCategories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>
        @if($filterType || $filterCategory)
            <button wire:click="$set('filterType', ''); $set('filterCategory', null)"
                    class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                Clear filters
            </button>
        @endif
        <span class="ml-auto text-xs text-gray-400 dark:text-gray-500">{{ $documents->count() }} document{{ $documents->count() !== 1 ? 's' : '' }}</span>
    </div>

    {{-- Create/Edit Form --}}
    @if($showForm)
        <div class="bg-white border border-gray-200 rounded-xl p-6 dark:bg-gray-900 dark:border-gray-800">
            <h2 class="text-lg font-semibold mb-4 dark:text-gray-100">{{ $editingId ? 'Edit Document' : 'New Document' }}</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                    <input type="text" wire:model="title" placeholder="Document title..."
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100" />
                    @error('title') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                        <select wire:model="type"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300">
                            @foreach($types as $key => $config)
                                <option value="{{ $key }}">{{ $config['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category (optional)</label>
                        <select wire:model="categoryId"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300">
                            <option value="">No category</option>
                            @foreach($allCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                    <textarea wire:model="description" rows="3" placeholder="Brief description of this document..."
                              class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">File path (local)</label>
                        <input type="text" wire:model="filePath" placeholder="C:\Users\...\Plan\file.md"
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm font-mono dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL (external)</label>
                        <input type="text" wire:model="url" placeholder="https://..."
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100" />
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if($editingId)
                        <button wire:click="updateDocument" class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700">Save</button>
                    @else
                        <button wire:click="createDocument" class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700">Add Document</button>
                    @endif
                    <button wire:click="resetForm" class="px-4 py-2 text-gray-600 hover:text-gray-800 text-sm dark:text-gray-400 dark:hover:text-gray-200">Cancel</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Documents grouped by category --}}
    @forelse($grouped as $catId => $docs)
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden dark:bg-gray-900 dark:border-gray-800">
            {{-- Category header --}}
            <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-800">
                @if($catId !== 'uncategorized')
                    @php $cat = $docs->first()->category; @endphp
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full" style="background-color: {{ $cat->color }}"></div>
                        <h3 class="text-sm font-semibold dark:text-gray-200">{{ $cat->name }}</h3>
                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $docs->count() }} doc{{ $docs->count() !== 1 ? 's' : '' }}</span>
                    </div>
                @else
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Uncategorized</h3>
                @endif
            </div>

            {{-- Document list --}}
            <div class="divide-y divide-gray-50 dark:divide-gray-800">
                @foreach($docs as $doc)
                    <div class="px-5 py-3 flex items-start gap-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group cursor-pointer"
                         onclick="window.location='{{ route('documents.show', $doc) }}'">
                        {{-- Type icon --}}
                        <div class="mt-0.5 {{ $doc->type_config['color'] }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $doc->type_config['icon'] }}"/>
                            </svg>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('documents.show', $doc) }}" class="text-sm font-medium dark:text-gray-200 hover:text-purple-600 dark:hover:text-purple-400">{{ $doc->title }}</a>
                                <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400">{{ $doc->type_config['label'] }}</span>
                            </div>
                            @if($doc->description)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2">{{ $doc->description }}</p>
                            @endif
                            <div class="flex items-center gap-3 mt-1">
                                @if($doc->file_path)
                                    <span class="text-[10px] font-mono text-gray-400 dark:text-gray-500 truncate max-w-xs" title="{{ $doc->file_path }}">{{ basename($doc->file_path) }}</span>
                                @endif
                                @if($doc->url)
                                    <a href="{{ $doc->url }}" target="_blank" onclick="event.stopPropagation()" class="text-[10px] text-purple-500 hover:text-purple-700 truncate max-w-xs">{{ $doc->url }}</a>
                                @endif
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity" onclick="event.stopPropagation()">
                            <button wire:click="startEditing({{ $doc->id }})"
                                    class="p-1.5 text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button wire:click="deleteDocument({{ $doc->id }})" wire:confirm="Delete this document?"
                                    class="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="bg-white border border-gray-200 rounded-xl p-12 text-center dark:bg-gray-900 dark:border-gray-800">
            <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-sm text-gray-500 dark:text-gray-400">No documents yet</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Add plans, notes, and references to organize your work</p>
        </div>
    @endforelse
</div>
