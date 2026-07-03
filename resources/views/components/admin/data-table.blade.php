@props([
    'paginator' => null,
])

<div {{ $attributes }}>
    <flux:table>
        @if (isset($header))
            <flux:table.columns>
                {{ $header }}
            </flux:table.columns>
        @endif

        <flux:table.rows>
            @forelse ($slot->isEmpty() ? [] : [1] as $_)
                {{ $slot }}
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="100" class="text-center py-8">
                        @if (isset($empty))
                            {{ $empty }}
                        @else
                            <div class="flex flex-col items-center gap-2">
                                <flux:icon name="inbox" class="size-8 text-zinc-400" />
                                <flux:text class="text-zinc-500">{{ __('No records found.') }}</flux:text>
                            </div>
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    @if ($paginator?->hasPages())
        <div class="mt-4">
            {{ $paginator->links() }}
        </div>
    @endif
</div>
