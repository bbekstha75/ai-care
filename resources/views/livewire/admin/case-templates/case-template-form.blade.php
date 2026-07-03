<flux:modal wire:model="showModal" class="md:w-3xl">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ $isEditing ? __('Edit Case Template') : __('Create Case Template') }}</flux:heading>
            <flux:subheading>{{ $isEditing ? __('Update this case template') : __('Add a new case template to the system') }}</flux:subheading>
        </div>

        <form wire:submit="save" class="space-y-6">
            <flux:input
                wire:model="name"
                :label="__('Template Name')"
                :placeholder="__('e.g. Initial Consultation')"
                required
            />
            
            <x-tiptap-editor wire:model="description" :label="__('Description')" :description="__('Template content')" />

            <flux:switch wire:model="is_active" :label="__('Active')" :description="__('Active templates can be used in the system')" />

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
