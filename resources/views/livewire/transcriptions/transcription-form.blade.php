<flux:modal name="transcription-form" :class="'md:w-3/4 max-w-4xl'">
    <form wire:submit="save" class="space-y-6">
        <div>
            <flux:heading size="lg">{{ $transcription?->exists ? __('Edit Transcription') : __('Create Transcription') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Manage your clinical transcription details.') }}</flux:text>
        </div>

        <div class="space-y-4">
            <flux:input wire:model="title" :label="__('Title')" required />

            <x-tiptap-editor
                wire:model="output"
                :label="__('Output Content')"
            />
        </div>

        <div class="flex gap-2">
            <flux:spacer />
            <flux:modal.close>
                <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
            </flux:modal.close>
            <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
        </div>
    </form>
</flux:modal>
