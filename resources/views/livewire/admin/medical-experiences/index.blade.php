<div class="space-y-6">
        <x-admin.breadcrumbs :items="[
            ['label' => __('Dashboard'), 'href' => route('dashboard')],
            ['label' => __('Medical Experiences')],
        ]" />

        <x-admin.page-header
            :heading="__('Medical Experiences')"
            :subheading="__('Manage medical experience categories')"
        >
            <x-slot:actions>
                <flux:button variant="primary" icon="plus" wire:click="$dispatch('open-medical-experience-form')">
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
                        :placeholder="__('Search medical experiences...')"
                        clearable
                    />
                </div>

                <x-admin.data-table :paginator="$medicalExperiences">
                    <x-slot:header>
                        <flux:table.column>{{ __('SN') }}</flux:table.column>
                        <flux:table.column>{{ __('Name') }}</flux:table.column>
                        <flux:table.column>{{ __('Sort Order') }}</flux:table.column>
                        <flux:table.column>{{ __('Created') }}</flux:table.column>
                        <flux:table.column align="end" class="text-right">{{ __('Actions') }}</flux:table.column>
                    </x-slot:header>

                    @foreach ($medicalExperiences as $experience )
                        <flux:table.row wire:key="experience-{{ $experience->id }}">
                            <flux:table.cell sticky class="bg-white dark:bg-zinc-900"> {{ $loop->iteration }}</flux:table.cell>
                            <flux:table.cell class="font-medium">{{ $experience->name }}</flux:table.cell>
                            <flux:table.cell>{{ $experience->sort_order ?? '—' }}</flux:table.cell>
                            <flux:table.cell>{{ $experience->created_at->format('M d, Y') }}</flux:table.cell>
                            <flux:table.cell class="text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <flux:button variant="ghost" size="sm" icon="pencil-square" wire:click="$dispatch('open-medical-experience-form', { id: '{{ $experience->id }}' })">
                                        {{ __('Edit') }}
                                    </flux:button>
                                    <flux:button variant="ghost" size="sm" icon="trash" wire:click="confirmDelete('{{ $experience->id }}')"
                                        x-on:click="$flux.modal('delete-confirmation').show()"
                                    >
                                        {{ __('Delete') }}
                                    </flux:button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach

                    <x-slot:empty>
                        <div class="flex flex-col items-center gap-2">
                            <flux:icon name="inbox" class="size-8 text-zinc-400" />
                            <flux:text class="text-zinc-500">
                                @if ($search)
                                    {{ __('No medical experiences match your search.') }}
                                @else
                                    {{ __('No medical experiences yet. Create your first one!') }}
                                @endif
                            </flux:text>
                        </div>
                    </x-slot:empty>
                </x-admin.data-table>
            </div>
        </flux:card>

        <x-admin.delete-modal
            name="delete-confirmation"
            :title="__('Delete Medical Experience')"
            :message="__('Are you sure you want to delete this medical experience? This action cannot be undone.')"
        >
            <flux:button variant="danger" wire:click="delete" x-on:click="$flux.modal('delete-confirmation').close()">
                {{ __('Delete') }}
            </flux:button>
        </x-admin.delete-modal>

        <livewire:admin.medical-experiences.medical-experience-form />
    </div>
