<?php

namespace App\Livewire\Admin\Roles;

use App\Enums\RoleEnum;
use App\Models\Permission;
use App\Models\Role;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class RoleForm extends Component
{
    public bool $showModal = false;

    public bool $isEditing = false;

    public ?Role $role = null;

    public string $name = '';

    /** @var array<int, string> */
    public array $selectedPermissions = [];

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'selectedPermissions' => ['nullable', 'array'],
            'selectedPermissions.*' => ['string', 'exists:permissions,name'],
        ];
    }

    #[On('open-role-form')]
    public function open(?string $id = null): void
    {
        $this->resetValidation();

        if ($id) {
            $this->role = Role::with('permissions')->find($id);

            if (! $this->role) {
                Flux::toast(text: __('Role not found.'), variant: 'danger');

                return;
            }

            $this->name = $this->role->name;
            $this->selectedPermissions = $this->role->permissions->pluck('name')->toArray();
            $this->isEditing = true;
        } else {
            $this->reset('name', 'selectedPermissions', 'role');
            $this->isEditing = false;
        }

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorize($this->isEditing ? 'roles update' : 'roles store');

        $this->validate();

        if ($this->isEditing && $this->role) {
            if ($this->role->name === RoleEnum::SuperAdmin->value) {
                Flux::toast(text: __('The Super Admin role name cannot be changed.'), variant: 'danger');

                return;
            }

            $this->role->update(['name' => $this->name]);
            $this->role->syncPermissions($this->selectedPermissions);

            Flux::toast(text: __('Role updated successfully.'), variant: 'success');
        } else {
            $role = Role::create(['name' => $this->name, 'guard_name' => 'web']);
            $role->syncPermissions($this->selectedPermissions);

            Flux::toast(text: __('Role created successfully.'), variant: 'success');
        }

        $this->showModal = false;

        $this->dispatch('role-saved');
    }

    public function render(): View
    {
        $groupedPermissions = Permission::orderBy('name')->get()->groupBy(function ($permission) {
            $parts = explode(' ', $permission->name);

            return count($parts) > 1 ? $parts[0] : 'other';
        });

        return view('livewire.admin.roles.role-form', [
            'groupedPermissions' => $groupedPermissions,
        ]);
    }
}
