<?php

namespace App\Livewire\Admin\Roles;

use App\Enums\RoleEnum;
use App\Livewire\Concerns\HasDeleteConfirmation;
use App\Livewire\Concerns\HasSearch;
use App\Models\Role;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Roles')]
#[Layout('layouts.app')]
class Index extends Component
{
    use HasDeleteConfirmation, HasSearch, WithPagination;

    public function delete(): void
    {
        $this->authorize('roles delete');

        $role = Role::find($this->deleteId);

        if (! $role) {
            Flux::toast(text: __('Role not found.'), variant: 'danger');
            $this->deleteId = null;

            return;
        }

        if ($role->name === RoleEnum::SuperAdmin->value) {
            Flux::toast(text: __('The Super Admin role cannot be deleted.'), variant: 'danger');
            $this->deleteId = null;

            return;
        }

        $role->delete();
        $this->deleteId = null;

        Flux::toast(text: __('Role deleted successfully.'), variant: 'success');
    }

    #[On('role-saved')]
    public function refreshList(): void {}

    public function render(): View
    {
        $roles = Role::query()
            ->when($this->search, fn ($query) => $query->where('name', 'like', '%'.$this->search.'%'))
            ->withCount(['permissions', 'users'])
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.roles.index', [
            'roles' => $roles,
        ]);
    }
}
