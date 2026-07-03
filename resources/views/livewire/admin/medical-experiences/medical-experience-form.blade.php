<flux:modal wire:model="showModal" class="md:w-xl">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ $isEditing ? __('Edit Medical Experience') : __('Create Medical Experience') }}</flux:heading>
            <flux:subheading>{{ $isEditing ? __('Update this medical experience category') : __('Add a new medical experience category') }}</flux:subheading>
        </div>

        <form wire:submit="save" class="space-y-6">
            <flux:input
                wire:model="name"
                :label="__('Name')"
                :placeholder="__('Enter medical experience name')"
                required
            />

            <flux:input
                wire:model="sortOrder"
                :label="__('Sort Order')"
                type="number"
                min="0"
                :placeholder="__('e.g. 1')"
                :description="__('Lower numbers appear first. Leave empty for no specific order.')"
            />

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
