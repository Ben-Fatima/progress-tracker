<div>
    <h1 class="text-2xl font-bold mb-8">Data & Export</h1>

    <div class="space-y-5">
        {{-- JSON Export --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 dark:bg-gray-900 dark:border-gray-800">
            <div class="flex items-start justify-between">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center shrink-0 dark:bg-purple-900/30">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-gray-900 dark:text-gray-100">Export All Data (JSON)</h2>
                        <p class="text-sm text-gray-500 mt-1 dark:text-gray-400">Download a complete backup of all categories, steps, sessions, logs, and goals.</p>
                    </div>
                </div>
                <button wire:click="exportJson" class="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors shrink-0">
                    Download JSON
                </button>
            </div>
            @if($lastExport)
                <p class="text-xs text-green-600 dark:text-green-400 mt-3 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Last export: {{ $lastExport }}
                </p>
            @endif
        </div>

        {{-- Markdown Review Export --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 dark:bg-gray-900 dark:border-gray-800">
            <div class="flex items-start justify-between">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center shrink-0 dark:bg-gray-800">
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-gray-900 dark:text-gray-100">Export Weekly Review (Markdown)</h2>
                        <p class="text-sm text-gray-500 mt-1 dark:text-gray-400">Download the current week's review as a formatted .md file.</p>
                    </div>
                </div>
                <button wire:click="exportMarkdownReview" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors shrink-0">
                    Download .md
                </button>
            </div>
        </div>

        {{-- Templates link --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 dark:bg-gray-900 dark:border-gray-800">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center shrink-0 dark:bg-purple-900/30">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                </div>
                <div>
                    <h2 class="font-semibold text-gray-900 dark:text-gray-100">Step Templates</h2>
                    <p class="text-sm text-gray-500 mt-1 dark:text-gray-400">Save a category's step structure as a reusable template for future projects.</p>
                    <a href="{{ route('templates') }}" class="inline-flex items-center gap-1 mt-3 text-sm text-purple-600 hover:underline dark:text-purple-400">
                        Manage templates
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
