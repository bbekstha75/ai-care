<div class="space-y-6">
    <x-admin.breadcrumbs :items="[
        ['label' => __('Dashboard'), 'href' => route('dashboard')],
        ['label' => __('Transcriptions')],
    ]" />

    <x-admin.page-header
        :heading="__('Transcriptions')"
        :subheading="__('Manage your clinical transcriptions')"
    >
        <x-slot:actions>
            <flux:button variant="primary" icon="plus" wire:click="$dispatch('open-transcription-form')">
                {{ __('Create New') }}
            </flux:button>
        </x-slot:actions>
    </x-admin.page-header>

    <flux:card>
        <div class="space-y-4">
            <div class="max-w-sm">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    icon="magnifying-glass"
                    :placeholder="__('Search transcriptions...')"
                    clearable
                />
            </div>

            <x-admin.data-table :paginator="$this->transcriptions">
                <x-slot:header>
                    <flux:table.column>{{ __('SN') }}</flux:table.column>
                    <flux:table.column>{{ __('Title') }}</flux:table.column>
                    <flux:table.column>{{ __('Status') }}</flux:table.column>
                    <flux:table.column>{{ __('Created') }}</flux:table.column>
                    <flux:table.column align="end" class="text-right">{{ __('Actions') }}</flux:table.column>
                </x-slot:header>

                @foreach ($this->transcriptions as $transcription)
                    <flux:table.row wire:key="transcription-{{ $transcription->id }}">
                        <flux:table.cell>{{ ($this->transcriptions->currentPage() - 1) * $this->transcriptions->perPage() + $loop->iteration }}</flux:table.cell>
                        <flux:table.cell class="font-medium">{{ $transcription->title }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$transcription->status->color()" size="sm" inset="top bottom">
                                {{ $transcription->status->label() }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>{{ $transcription->created_at->format('M d, Y') }}</flux:table.cell>
                        <flux:table.cell class="text-right">
                            <div class="flex items-center justify-end gap-1">
                                <flux:button variant="ghost" size="sm" icon="pencil-square" wire:click="$dispatch('open-transcription-form', { id: '{{ $transcription->id }}' })">
                                    {{ __('Edit') }}
                                </flux:button>
                                <flux:button variant="ghost" size="sm" icon="trash"
                                    wire:click="confirmDelete('{{ $transcription->id }}')"
                                    x-on:click="$flux.modal('delete-confirmation').show()"
                                >
                                    {{ __('Delete') }}
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </x-admin.data-table>
        </div>
    </flux:card>

    <livewire:transcriptions.transcription-form />
    
    <x-admin.delete-modal
        name="delete-confirmation"
        :title="__('Delete Transcription')"
        :message="__('Are you sure you want to delete this transcription? This action cannot be undone.')"
    >
        <flux:button variant="danger" wire:click="delete" x-on:click="$flux.modal('delete-confirmation').close()">
            {{ __('Delete') }}
        </flux:button>
    </x-admin.delete-modal>
</div>
