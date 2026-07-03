@props([
    'name' => 'delete-confirmation',
    'title' => 'Confirm Deletion',
    'message' => 'Are you sure you want to delete this record? This action cannot be undone.',
])

<flux:modal name="{{ $name }}" class="min-w-[22rem]">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ __($title) }}</flux:heading>
            <flux:text class="mt-2">{{ __($message) }}</flux:text>
        </div>

        <div class="flex gap-2">
            <flux:spacer />
            <flux:modal.close>
                <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
            </flux:modal.close>
            {{ $slot }}
        </div>
    </div>
</flux:modal>
