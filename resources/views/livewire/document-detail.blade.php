<div class="max-w-4xl mx-auto space-y-6">
    {{-- Back link + header --}}
    <div>
        <a href="{{ route('documents') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-purple-600 dark:text-gray-400 dark:hover:text-purple-400 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Documents
        </a>
        <div class="flex items-start justify-between">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <div class="{{ $document->type_config['color'] }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $document->type_config['icon'] }}"/>
                        </svg>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">{{ $document->type_config['label'] }}</span>
                    @if($document->category)
                        <span class="text-xs px-2 py-0.5 rounded-full text-white" style="background-color: {{ $document->category->color }}">{{ $document->category->name }}</span>
                    @endif
                </div>
                <h1 class="text-2xl font-bold dark:text-gray-100">{{ $document->title }}</h1>
                @if($document->description)
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $document->description }}</p>
                @endif
            </div>
            <a href="{{ route('documents') }}" wire:navigate
               class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </a>
        </div>
    </div>

    {{-- Meta info --}}
    <div class="flex items-center gap-4 text-xs text-gray-400 dark:text-gray-500">
        @if($document->file_path)
            <span class="font-mono truncate max-w-md" title="{{ $document->file_path }}">{{ $document->file_path }}</span>
        @endif
        @if($document->url)
            <a href="{{ $document->url }}" target="_blank" class="text-purple-500 hover:text-purple-700">{{ $document->url }}</a>
        @endif
        <span>Updated {{ $document->updated_at->diffForHumans() }}</span>
    </div>

    {{-- Document content --}}
    @if($fileExists)
        @if($renderedHtml)
            {{-- Rendered markdown --}}
            <div class="bg-white border border-gray-200 rounded-xl p-8 dark:bg-gray-900 dark:border-gray-800">
                <div class="prose prose-gray dark:prose-invert max-w-none
                            prose-headings:font-bold prose-headings:tracking-tight
                            prose-h1:text-2xl prose-h1:border-b prose-h1:border-gray-200 prose-h1:pb-3 prose-h1:mb-4 dark:prose-h1:border-gray-700
                            prose-h2:text-xl prose-h2:mt-8 prose-h2:mb-3
                            prose-h3:text-lg prose-h3:mt-6 prose-h3:mb-2
                            prose-p:leading-relaxed prose-p:text-gray-700 dark:prose-p:text-gray-300
                            prose-li:text-gray-700 dark:prose-li:text-gray-300
                            prose-code:text-purple-600 prose-code:bg-purple-50 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded prose-code:text-sm dark:prose-code:bg-purple-900/30 dark:prose-code:text-purple-400
                            prose-pre:bg-gray-900 prose-pre:text-gray-100 dark:prose-pre:bg-gray-950 dark:prose-pre:border dark:prose-pre:border-gray-800
                            prose-table:text-sm prose-th:bg-gray-50 prose-th:px-3 prose-th:py-2 dark:prose-th:bg-gray-800
                            prose-td:px-3 prose-td:py-2 prose-td:border-t prose-td:border-gray-100 dark:prose-td:border-gray-800
                            prose-a:text-purple-600 prose-a:no-underline hover:prose-a:underline dark:prose-a:text-purple-400
                            prose-strong:text-gray-900 dark:prose-strong:text-gray-100
                            prose-blockquote:border-purple-500 prose-blockquote:text-gray-600 dark:prose-blockquote:text-gray-400">
                    {!! $renderedHtml !!}
                </div>
            </div>
        @else
            {{-- Raw file content (non-markdown) --}}
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden dark:bg-gray-900 dark:border-gray-800">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                    <span class="text-xs font-mono text-gray-500 dark:text-gray-400">{{ basename($document->file_path) }}</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ number_format(strlen($fileContent)) }} characters</span>
                </div>
                <pre class="p-5 text-sm font-mono text-gray-700 dark:text-gray-300 overflow-x-auto whitespace-pre-wrap">{{ $fileContent }}</pre>
            </div>
        @endif
    @elseif($document->file_path)
        {{-- File not found --}}
        <div class="bg-red-50 border border-red-200 rounded-xl p-6 dark:bg-red-900/20 dark:border-red-800">
            <div class="flex items-center gap-2 text-red-600 dark:text-red-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                <span class="text-sm font-medium">File not found</span>
            </div>
            <p class="text-xs text-red-500 dark:text-red-400 mt-1 font-mono">{{ $document->file_path }}</p>
        </div>
    @elseif($document->url)
        {{-- URL only --}}
        <div class="bg-white border border-gray-200 rounded-xl p-8 text-center dark:bg-gray-900 dark:border-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">This document links to an external URL</p>
            <a href="{{ $document->url }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Open link
            </a>
        </div>
    @else
        {{-- No content --}}
        <div class="bg-white border border-gray-200 rounded-xl p-8 text-center dark:bg-gray-900 dark:border-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">No file path or URL attached to this document</p>
        </div>
    @endif
</div>
