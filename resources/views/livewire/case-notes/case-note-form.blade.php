<flux:modal name="case-note-form" :class="'md:w-3/4 max-w-4xl'">
    <form wire:submit="save" class="space-y-6">
        <div>
            <flux:heading size="lg">{{ $caseNote?->exists ? __('Edit Case Note') : __('Create Case Note') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Manage the output of the clinical case note.') }}</flux:text>
        </div>

        <div class="space-y-4">
            <x-tiptap-editor
                wire:model="output"
                :label="__('Case Output')"
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
