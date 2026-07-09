<flux:modal name="case-note-detail" :class="'md:w-3/4 max-w-4xl'">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ $type === 'input' ? __('Case Input') : __('Case Output') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Viewing the full content.') }}</flux:text>
        </div>

        <div class="p-4 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg max-h-[60vh] overflow-y-auto">
            @if ($caseNote)
                @if ($type === 'input')
                    <div class="whitespace-pre-wrap font-mono text-sm">
                        {{ $caseNote->input }}
                    </div>
                @else
                    <div class="prose prose-sm dark:prose-invert max-w-none">
                        {!! $caseNote->output !!}
                    </div>
                @endif
            @endif
        </div>

        <div class="flex gap-2">
            <flux:spacer />
            <flux:modal.close>
                <flux:button variant="ghost">{{ __('Close') }}</flux:button>
            </flux:modal.close>
        </div>
    </div>
</flux:modal>
