@props(['variant' => 'segmented'])

<div 
    x-data="{ 
        tab: @if($attributes->wire('model')->value()) @entangle($attributes->wire('model')) @else null @endif 
    }" 
    {{ $attributes->whereDoesntStartWith('wire:model')->merge(['class' => 'flex gap-4 border-b border-zinc-200 dark:border-zinc-700']) }}
>
    {{ $slot }}
</div>
