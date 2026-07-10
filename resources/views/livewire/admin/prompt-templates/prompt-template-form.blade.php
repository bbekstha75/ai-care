<flux:modal wire:model="showModal" class="md:w-3xl">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ $isEditing ? __('Edit Prompt Template') : __('Create Prompt Template') }}</flux:heading>
            <flux:subheading>{{ $isEditing ? __('Update this AI prompt template') : __('Add a new AI prompt template to the system') }}</flux:subheading>
        </div>

        <form wire:submit="save" class="space-y-6">
            <flux:input
                wire:model="title"
                :label="__('Title')"
                :placeholder="__('e.g. SOAP Note')"
                required
            />
            
            <flux:input
                wire:model="description"
                :label="__('Description')"
                :placeholder="__('e.g. Generates a standard SOAP note...')"
            />
            
            <flux:textarea
                wire:model="systemPrompt"
                :label="__('System Prompt')"
                :placeholder="__('Enter the main AI instructions here...')"
                rows="6"
                required
            />
            
            <flux:textarea
                wire:model="stylingInstructions"
                :label="__('Styling Instructions')"
                :placeholder="__('Enter formatting or styling instructions here...')"
                rows="4"
            />

            <flux:switch wire:model="status" :label="__('Active')" :description="__('Active templates can be used in the system')" />

            <div class="flex items-center justify-end gap-4">
                <flux:button variant="ghost" wire:click="$set('showModal', false)">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">{{ __('Save') }}</span>
                    <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
                </flux:button>
            </div>
        </form>
    </div>
</flux:modal>
