@props(['name', 'icon' => null])

<button
    type="button"
    x-on:click="tab = '{{ $name }}'"
    :class="tab === '{{ $name }}' 
        ? 'border-zinc-900 text-zinc-900 dark:border-white dark:text-white' 
        : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300'"
    {{ $attributes->merge(['class' => 'pb-3 text-sm font-medium border-b-2 transition-colors flex items-center gap-2']) }}
>
    @if($icon)
        <flux:icon :name="$icon" class="size-4" />
    @endif
    {{ $slot }}
</button>
