<flux:modal wire:model="showModal" class="md:w-xl">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ $isEditing ? __('Edit Diagnosis') : __('Create Diagnosis') }}</flux:heading>
            <flux:subheading>{{ $isEditing ? __('Update this diagnosis') : __('Add a new diagnosis') }}</flux:subheading>
        </div>

        <form wire:submit="save" class="space-y-6">
            <flux:input
                wire:model="name"
                :label="__('Name')"
                :placeholder="__('Enter diagnosis name')"
                required
            />

            <flux:textarea
                wire:model="symptoms"
                :label="__('Symptoms')"
                :placeholder="__('Enter symptoms')"
                rows="3"
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
