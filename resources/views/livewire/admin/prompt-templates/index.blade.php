<div class="space-y-6">
    <x-admin.breadcrumbs :items="[
        ['label' => __('Dashboard'), 'href' => route('dashboard')],
        ['label' => __('Prompt Templates')],
    ]" />

    <x-admin.page-header
        :heading="__('Prompt Templates')"
        :subheading="__('Manage AI prompt templates for case generation')"
    >
        <x-slot:actions>
            @can('prompt_templates store')
                <flux:button variant="primary" icon="plus" wire:click="$dispatch('create-prompt-template')">
                    {{ __('Create New') }}
                </flux:button>
            @endcan
        </x-slot:actions>
    </x-admin.page-header>

    <flux:card>
        <div class="space-y-4">
            <div class="max-w-sm">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    icon="magnifying-glass"
                    :placeholder="__('Search prompt templates...')"
                    clearable
                />
            </div>

            <x-admin.data-table :paginator="$promptTemplates">
                <x-slot:header>
                    <flux:table.column>{{ __('SN') }}</flux:table.column>
                    <flux:table.column>{{ __('Title') }}</flux:table.column>
                    <flux:table.column>{{ __('Status') }}</flux:table.column>
                    <flux:table.column>{{ __('Created') }}</flux:table.column>
                    <flux:table.column align="end" class="text-right">{{ __('Actions') }}</flux:table.column>
                </x-slot:header>

                @foreach ($promptTemplates as $template)
                    <flux:table.row wire:key="prompt-template-{{ $template->id }}">
                        <flux:table.cell>{{ ($promptTemplates->currentPage() - 1) * $promptTemplates->perPage() + $loop->iteration }}</flux:table.cell>
                        <flux:table.cell class="font-medium">
                            {{ $template->title }}
                            @if($template->description)
                                <div class="text-xs text-zinc-500 font-normal mt-0.5">{{ $template->description }}</div>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($template->status)
                                <flux:badge color="green" size="sm" inset="top bottom">{{ __('Active') }}</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm" inset="top bottom">{{ __('Inactive') }}</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $template->created_at->format('M d, Y') }}</flux:table.cell>
                        <flux:table.cell class="text-right">
                            <div class="flex items-center justify-end gap-1">
                                <flux:button variant="ghost" size="sm" icon="pencil-square" wire:click="$dispatch('edit-prompt-template', { id: '{{ $template->id }}' })">
                                    {{ __('Edit') }}
                                </flux:button>
                                <flux:button variant="ghost" size="sm" icon="trash"
                                    wire:click="confirmDelete('{{ $template->id }}')"
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

    <livewire:admin.prompt-templates.prompt-template-form />
    
    <x-admin.delete-modal
        name="delete-confirmation"
        :title="__('Delete Prompt Template')"
        :message="__('Are you sure you want to delete this prompt template? This action cannot be undone.')"
    >
        <flux:button variant="danger" wire:click="delete" x-on:click="$flux.modal('delete-confirmation').close()">
            {{ __('Delete') }}
        </flux:button>
    </x-admin.delete-modal>
</div>
