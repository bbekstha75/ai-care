<div class="space-y-6">
    <x-admin.breadcrumbs :items="[
        ['label' => __('Dashboard'), 'href' => route('dashboard')],
        ['label' => __('Roles')],
    ]" />

    <x-admin.page-header
        :heading="__('Roles')"
        :subheading="__('Manage roles and their permissions')"
    >
        <x-slot:actions>
            <flux:button variant="primary" icon="plus" wire:click="$dispatch('open-role-form')">
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
                    :placeholder="__('Search roles...')"
                    clearable
                />
            </div>

            <x-admin.data-table :paginator="$roles">
                <x-slot:header>
                    <flux:table.column>{{ __('SN') }}</flux:table.column>
                    <flux:table.column>{{ __('Name') }}</flux:table.column>
                    <flux:table.column>{{ __('Permissions') }}</flux:table.column>
                    <flux:table.column>{{ __('Users') }}</flux:table.column>
                    <flux:table.column>{{ __('Created') }}</flux:table.column>
                    <flux:table.column align="end" class="text-right">{{ __('Actions') }}</flux:table.column>
                </x-slot:header>

                @foreach ($roles as $role)
                    <flux:table.row wire:key="role-{{ $role->id }}">
                        <flux:table.cell>{{ ($roles->currentPage() - 1) * $roles->perPage() + $loop->iteration }}</flux:table.cell>
                        <flux:table.cell class="font-medium">
                            {{ $role->name }}
                            @if ($role->name === \App\Enums\RoleEnum::SuperAdmin->value)
                                <flux:badge color="amber" size="sm" class="ml-2">{{ __('Protected') }}</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="blue" size="sm">{{ $role->permissions_count }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="zinc" size="sm">{{ $role->users_count }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>{{ $role->created_at->format('M d, Y') }}</flux:table.cell>
                        <flux:table.cell class="text-right">
                            <div class="flex items-center justify-end gap-1">
                                <flux:button variant="ghost" size="sm" icon="pencil-square" wire:click="$dispatch('open-role-form', { id: '{{ $role->id }}' })">
                                    {{ __('Edit') }}
                                </flux:button>
                                @if ($role->name !== \App\Enums\RoleEnum::SuperAdmin->value)
                                    <flux:button variant="ghost" size="sm" icon="trash"
                                        wire:click="confirmDelete('{{ $role->id }}')"
                                        x-on:click="$flux.modal('delete-confirmation').show()"
                                    >
                                        {{ __('Delete') }}
                                    </flux:button>
                                @endif
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach

                <x-slot:empty>
                    <div class="flex flex-col items-center gap-2">
                        <flux:icon name="inbox" class="size-8 text-zinc-400" />
                        <flux:text class="text-zinc-500">
                            @if ($search)
                                {{ __('No roles match your search.') }}
                            @else
                                {{ __('No roles found.') }}
                            @endif
                        </flux:text>
                    </div>
                </x-slot:empty>
            </x-admin.data-table>
        </div>
    </flux:card>

    <x-admin.delete-modal
        name="delete-confirmation"
        :title="__('Delete Role')"
        :message="__('Are you sure you want to delete this role? Users assigned to it will lose their associated permissions.')"
    >
        <flux:button variant="danger" wire:click="delete" x-on:click="$flux.modal('delete-confirmation').close()">
            {{ __('Delete') }}
        </flux:button>
    </x-admin.delete-modal>

    <livewire:admin.roles.role-form />
</div>
