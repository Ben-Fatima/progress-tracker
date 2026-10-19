<div>
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold">Step Templates</h1>
        <a href="{{ route('export') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">&larr; Back to Export</a>
    </div>

    <div class="space-y-5">
        {{-- Save template from category --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 dark:bg-gray-900 dark:border-gray-800">
            <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Save Template from Category</h2>
            <div class="space-y-3">
                <select wire:model="savingFromCategoryId" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
                    <option value="">Select a category...</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }} ({{ $cat->total_steps }} steps)</option>
                    @endforeach
                </select>
                @if($savingFromCategoryId)
                    <input wire:model="newTemplateName" type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" placeholder="Template name">
                    <input wire:model="newTemplateDescription" type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" placeholder="Description (optional)">
                    <button wire:click="saveFromCategory" class="px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition-colors">Save Template</button>
                @endif
            </div>
        </div>

        {{-- Existing templates --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 dark:bg-gray-900 dark:border-gray-800">
            <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Saved Templates</h2>
            @forelse($templates as $template)
                <div class="py-3 {{ !$loop->last ? 'border-b border-gray-50 dark:border-gray-800' : '' }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $template->name }}</h3>
                            @if($template->description)
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $template->description }}</p>
                            @endif
                            <p class="text-xs text-gray-400 mt-0.5">{{ count($template->steps) }} steps</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button wire:click="$set('loadingTemplateId', {{ $template->id }})" class="px-3 py-1 text-xs bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors dark:bg-purple-900/30 dark:text-purple-400">Use</button>
                            <button wire:click="deleteTemplate({{ $template->id }})" wire:confirm="Delete this template?" class="px-3 py-1 text-xs text-red-600 hover:bg-red-50 rounded-lg transition-colors dark:hover:bg-red-900/30">Delete</button>
                        </div>
                    </div>

                    @if($loadingTemplateId === $template->id)
                        <div class="mt-3 p-3 bg-gray-50 rounded-xl space-y-2 dark:bg-gray-800">
                            <p class="text-xs text-gray-600 dark:text-gray-300 font-medium">Create category from this template:</p>
                            <div class="flex gap-2">
                                <input wire:model="loadCategoryName" type="text" class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200" placeholder="New category name">
                                <input wire:model="loadCategoryColor" type="color" class="w-10 h-10 rounded-lg border border-gray-200 cursor-pointer">
                            </div>
                            <div class="flex gap-2">
                                <button wire:click="loadTemplate" class="px-3 py-1.5 bg-purple-600 text-white text-xs rounded-lg hover:bg-purple-700 transition-colors">Create Category</button>
                                <button wire:click="$set('loadingTemplateId', null)" class="px-3 py-1.5 text-gray-600 dark:text-gray-300 text-xs hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">Cancel</button>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-sm text-gray-400 text-center py-4">No templates saved yet. Save one from an existing category above.</p>
            @endforelse
        </div>
    </div>
</div>
