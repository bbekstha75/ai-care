<flux:modal wire:model="showModal" class="md:w-2xl">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ $isEditing ? __('Edit Role') : __('Create Role') }}</flux:heading>
            <flux:subheading>{{ $isEditing ? __('Update this role and its permissions') : __('Add a new role and assign permissions') }}</flux:subheading>
        </div>

        <form wire:submit="save" class="space-y-6">
            <flux:input
                wire:model="name"
                :label="__('Role Name')"
                :placeholder="__('e.g. editor')"
                required
                :disabled="$isEditing && $role?->name === \App\Enums\RoleEnum::SuperAdmin->value"
            />

            <div>
                <flux:label>{{ __('Permissions') }}</flux:label>
                <flux:description>{{ __('Select which permissions this role should have.') }}</flux:description>

                <div x-data="{
                    activeAccordion: null,
                    toggleGroup(permissions) {
                        let allSelected = permissions.every(p => $wire.selectedPermissions.includes(p));
                        if (allSelected) {
                            $wire.selectedPermissions = $wire.selectedPermissions.filter(p => !permissions.includes(p));
                        } else {
                            let toAdd = permissions.filter(p => !$wire.selectedPermissions.includes(p));
                            $wire.selectedPermissions = [...$wire.selectedPermissions, ...toAdd];
                        }
                    }
                }" class="mt-3 space-y-3">
                    @foreach ($groupedPermissions as $module => $permissions)
                        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                            <div class="flex items-center justify-between bg-zinc-50 dark:bg-zinc-800 px-4 py-3">
                                <button type="button" @click="activeAccordion = activeAccordion === '{{ $module }}' ? null : '{{ $module }}'" class="flex flex-1 items-center gap-2 text-sm font-semibold text-zinc-900 dark:text-white text-left focus:outline-none">
                                    <flux:icon name="chevron-down" class="size-4 transition-transform duration-200" x-bind:class="activeAccordion === '{{ $module }}' ? 'rotate-180' : ''" />
                                    <span class="capitalize">{{ str_replace('_', ' ', $module) }}</span>
                                    <flux:badge size="sm" color="zinc">{{ $permissions->count() }}</flux:badge>
                                </button>
                                <div class="ml-4">
                                    <flux:button size="sm" variant="ghost" class="h-7 text-xs" @click="toggleGroup({{ json_encode($permissions->pluck('name')) }})">
                                        {{ __('Toggle All') }}
                                    </flux:button>
                                </div>
                            </div>
                            
                            <div x-show="activeAccordion === '{{ $module }}'" x-collapse x-cloak class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-700">
                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                    @foreach ($permissions as $permission)
                                        <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-zinc-200 px-3 py-2.5 transition hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800">
                                            <flux:checkbox
                                                wire:model="selectedPermissions"
                                                value="{{ $permission->name }}"
                                            />
                                            <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ $permission->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @error('selectedPermissions')
                    <flux:error>{{ $message }}</flux:error>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-4">
                <flux:button variant="ghost" wire:click="$set('showModal', false)">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">{{ __('Save') }}</span>
                    <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
                </flux:button>
            </div>
        </form>
    </div>
</flux:modal>
