<div class="space-y-6">
    <x-admin.breadcrumbs :items="[
        ['label' => __('Dashboard'), 'href' => route('dashboard')],
        ['label' => __('Case Notes')],
    ]" />

    <x-admin.page-header
        :heading="__('Case Notes')"
        :subheading="__('Manage your clinical case notes')"
    >
{{--        <x-slot:actions>--}}
{{--            <flux:button variant="primary" icon="plus" wire:click="$dispatch('open-case-note-form')">--}}
{{--                {{ __('Create New') }}--}}
{{--            </flux:button>--}}
{{--        </x-slot:actions>--}}
    </x-admin.page-header>

    <flux:card class="!p-0">
        <x-tabs wire:model.live="tab" class="px-4 pt-4">
            <x-tabs.tab name="results" icon="document-text">{{ __('Case Results') }}</x-tabs.tab>
            <x-tabs.tab name="input" icon="clipboard-document">{{ __('Case Input') }}</x-tabs.tab>
        </x-tabs>

        <div class="p-6">
            @if ($tab === 'results')
                <div class="space-y-4">
                    @forelse($this->caseNotes as $note)
                        <div class="flex items-start gap-4" wire:key="note-{{ $note->id }}">
                            <div class="flex-1 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4">
                                <div class="flex items-center gap-2 mb-2 text-xs text-zinc-500 dark:text-zinc-400">
                                    <flux:icon.clock class="size-3" />
                                    <span>Generated: {{ $note->created_at->format('d M Y, h:i a') }}</span>
                                </div>
                                <div class="prose prose-sm dark:prose-invert max-w-none">
                                    {!! $note->output ?: '<p class="text-zinc-500 italic">No output provided.</p>' !!}
                                </div>
                            </div>
                            <div class="flex flex-col gap-2 w-28 shrink-0">
                                <flux:button variant="outline" size="sm" icon="pencil-square" wire:click="$dispatch('open-case-note-form', { id: '{{ $note->id }}' })" class="w-full justify-center text-blue-600 dark:text-blue-400">
                                    {{ __('Edit') }}
                                </flux:button>
                                <flux:button variant="outline" size="sm" icon="trash" wire:click="confirmDelete('{{ $note->id }}')" x-on:click="$flux.modal('delete-confirmation').show()" class="w-full justify-center text-red-600 dark:text-red-400">
                                    {{ __('Delete') }}
                                </flux:button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-zinc-500">
                            {{ __('No case notes found.') }}
                        </div>
                    @endforelse

                    <div class="mt-4">
                        {{ $this->caseNotes->links() }}
                    </div>
                </div>
            @else
                <div class="space-y-4">
                    <div class="max-w-sm">
                        <flux:input
                            wire:model.live.debounce.300ms="search"
                            icon="magnifying-glass"
                            :placeholder="__('Search input...')"
                            clearable
                        />
                    </div>

                    <x-admin.data-table :paginator="$this->caseNotes">
                        <x-slot:header>
                            <flux:table.column>{{ __('SN') }}</flux:table.column>
                            <flux:table.column>{{ __('Date & Time') }}</flux:table.column>
                            <flux:table.column align="center">{{ __('Case Input') }}</flux:table.column>
                            <flux:table.column align="center">{{ __('Case Result') }}</flux:table.column>
                            <flux:table.column align="end" class="text-right">{{ __('Action') }}</flux:table.column>
                        </x-slot:header>

                        @foreach ($this->caseNotes as $note)
                            <flux:table.row wire:key="input-{{ $note->id }}">
                                <flux:table.cell>{{ ($this->caseNotes->currentPage() - 1) * $this->caseNotes->perPage() + $loop->iteration }}</flux:table.cell>
                                <flux:table.cell>{{ $note->created_at->format('d-m-Y h:i A') }}</flux:table.cell>
                                <flux:table.cell align="center">
                                    @if($note->input)
                                        <flux:button variant="ghost" size="sm" icon="eye" class="text-emerald-600 dark:text-emerald-400" wire:click="$dispatch('open-case-note-detail', { id: '{{ $note->id }}', type: 'input' })" />
                                    @else
                                        <span class="text-zinc-400">-</span>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell align="center">
                                    @if($note->output)
                                        <flux:button variant="ghost" size="sm" icon="eye" class="text-emerald-600 dark:text-emerald-400" wire:click="$dispatch('open-case-note-detail', { id: '{{ $note->id }}', type: 'output' })" />
                                    @else
                                        <span class="text-zinc-400">-</span>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell class="text-right">
                                    <flux:button variant="ghost" size="sm" icon="trash" class="text-red-600 dark:text-red-400"
                                        wire:click="confirmDelete('{{ $note->id }}')"
                                        x-on:click="$flux.modal('delete-confirmation').show()"
                                    />
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </x-admin.data-table>
                </div>
            @endif
        </div>
    </flux:card>

    <livewire:case-notes.case-note-form />
    <livewire:case-notes.case-note-detail-modal />

    <x-admin.delete-modal
        name="delete-confirmation"
        :title="__('Delete Case Note')"
        :message="__('Are you sure you want to delete this case note? This action cannot be undone.')"
    >
        <flux:button variant="danger" wire:click="delete" x-on:click="$flux.modal('delete-confirmation').close()">
            {{ __('Delete') }}
        </flux:button>
    </x-admin.delete-modal>
</div>
