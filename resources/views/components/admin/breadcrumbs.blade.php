@props([
    'items' => [],
])

<flux:breadcrumbs class="mb-6">
    @foreach ($items as $item)
        @if ($loop->last)
            <flux:breadcrumbs.item>{{ $item['label'] }}</flux:breadcrumbs.item>
        @else
            <flux:breadcrumbs.item :href="$item['href'] ?? '#'" wire:navigate>{{ $item['label'] }}</flux:breadcrumbs.item>
        @endif
    @endforeach
</flux:breadcrumbs>
