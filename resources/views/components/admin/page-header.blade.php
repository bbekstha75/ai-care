@props([
    'heading' => '',
    'subheading' => '',
])

<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <flux:heading size="xl" level="1">{{ $heading }}</flux:heading>
        @if ($subheading)
            <flux:subheading size="lg">{{ $subheading }}</flux:subheading>
        @endif
    </div>

    @if (isset($actions))
        <div class="flex items-center gap-2">
            {{ $actions }}
        </div>
    @endif
</div>

<flux:separator variant="subtle" class="mt-4" />
