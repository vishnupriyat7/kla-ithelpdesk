<div class="space-y-4 p-2">
    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700 pb-2">
        <span>Reported: <strong>{{ $record->reported_at ? $record->reported_at->format('d/m/Y h:i A') : 'N/A' }}</strong></span>
        <span>Merged Messages: <strong class="text-primary-600">{{ $record->merge_count }}</strong></span>
    </div>

    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 font-mono text-sm whitespace-pre-wrap max-h-96 overflow-y-auto border border-gray-200 dark:border-gray-800 leading-relaxed text-gray-800 dark:text-gray-200">
        {{ $record->raw_messages }}
    </div>

    @if(!empty($record->remarks))
        <div class="bg-blue-50 dark:bg-blue-950/40 rounded-lg p-3 border border-blue-200 dark:border-blue-900 text-xs">
            <span class="font-bold text-blue-700 dark:text-blue-300 block mb-1">Parsed Resolution / Remarks:</span>
            <span class="text-blue-900 dark:text-blue-200 whitespace-pre-wrap">{{ $record->remarks }}</span>
        </div>
    @endif
</div>
