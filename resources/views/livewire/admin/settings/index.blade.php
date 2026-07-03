<div class="space-y-6">
    <x-admin.breadcrumbs :items="[
        ['label' => __('Dashboard'), 'href' => route('dashboard')],
        ['label' => __('Settings')],
    ]" />

    <x-admin.page-header
        :heading="__('Settings')"
        :subheading="__('Manage global application settings')"
    />

    <flux:card class="max-w-2xl">
        <form wire:submit="save" class="space-y-6">
            
            <flux:checkbox wire:model="is_model_locked" :label="__('Is Model Locked?')" :description="__('Lock the AI model selection globally.')" />
            
            <flux:checkbox wire:model="is_setting_locked" :label="__('Is Setting Locked?')" :description="__('Lock these settings from being changed by other admins.')" />
            
            <flux:radio.group wire:model="default_model" :label="__('Default Model')" variant="segmented">
                <flux:radio value="GPT-3.5-TURBO" label="GPT-3.5-TURBO" />
                <flux:radio value="GPT-5" label="GPT-5" />
                <flux:radio value="LLAMA 2" label="LLAMA 2" />
            </flux:radio.group>

            <div class="flex items-center justify-end gap-4">
                <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">{{ __('Save Settings') }}</span>
                    <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>
