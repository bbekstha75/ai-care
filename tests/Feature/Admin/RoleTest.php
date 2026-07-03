<?php

use App\Enums\RoleEnum;
use App\Livewire\Admin\Roles\Index;
use App\Livewire\Admin\Roles\RoleForm;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// --- Auth Guard ---

test('guests are redirected from roles index', function () {
    $this->get(route('admin.roles.index'))
        ->assertRedirect(route('login'));
});

test('non-admin users cannot access roles index', function () {
    asUser();

    $this->get(route('admin.roles.index'))
        ->assertForbidden();
});

// --- Index ---

test('authenticated admins can view roles index', function () {
    asAdmin();

    $this->get(route('admin.roles.index'))
        ->assertOk();
});

test('index displays roles', function () {
    asAdmin();
    Role::findOrCreate('editor', 'web');

    Livewire::test(Index::class)
        ->assertSee('editor');
});

test('index supports search filtering', function () {
    asAdmin();

    Role::findOrCreate('editor', 'web');
    Role::findOrCreate('moderator', 'web');

    Livewire::test(Index::class)
        ->set('search', 'edit')
        ->assertSee('editor')
        ->assertDontSee('moderator');
});

// --- Create & Edit (RoleForm) ---

test('admin can create a role via form', function () {
    asAdmin();

    Livewire::test(RoleForm::class)
        ->call('open')
        ->set('name', 'editor')
        ->call('save')
        ->assertDispatched('role-saved');

    $this->assertDatabaseHas('roles', ['name' => 'editor']);
});

test('admin can create a role with permissions', function () {
    asAdmin();

    $permission = Permission::findOrCreate('diagnoses store', 'web');

    Livewire::test(RoleForm::class)
        ->call('open')
        ->set('name', 'editor')
        ->set('selectedPermissions', [$permission->name])
        ->call('save')
        ->assertDispatched('role-saved');

    $role = Role::where('name', 'editor')->first();
    expect($role)->not->toBeNull();
    expect($role->hasPermissionTo('diagnoses store'))->toBeTrue();
});

test('non-admin cannot create a role', function () {
    asUser();

    Livewire::test(RoleForm::class)
        ->call('open')
        ->set('name', 'editor')
        ->call('save')
        ->assertForbidden();
});

test('form validates required role name', function () {
    asAdmin();

    Livewire::test(RoleForm::class)
        ->call('open')
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

test('admin can update a role name', function () {
    asAdmin();

    $role = Role::findOrCreate('editor', 'web');

    Livewire::test(RoleForm::class)
        ->call('open', $role->id)
        ->set('name', 'content-editor')
        ->call('save')
        ->assertDispatched('role-saved');

    $this->assertDatabaseHas('roles', ['id' => $role->id, 'name' => 'content-editor']);
});

test('admin can sync permissions on role update', function () {
    asAdmin();

    $role = Role::findOrCreate('editor', 'web');
    $permission = Permission::findOrCreate('diagnoses store', 'web');

    Livewire::test(RoleForm::class)
        ->call('open', $role->id)
        ->set('selectedPermissions', [$permission->name])
        ->call('save')
        ->assertHasNoErrors();

    expect($role->fresh()->hasPermissionTo('diagnoses store'))->toBeTrue();
});

test('super_admin role name cannot be changed', function () {
    asAdmin();

    $role = Role::findOrCreate(RoleEnum::SuperAdmin->value, 'web');

    Livewire::test(RoleForm::class)
        ->call('open', $role->id)
        ->set('name', 'hacked')
        ->call('save');

    // Name remains unchanged
    $this->assertDatabaseHas('roles', [
        'id' => $role->id,
        'name' => RoleEnum::SuperAdmin->value,
    ]);
});

// --- Delete ---

test('admin can delete a non-protected role', function () {
    asAdmin();

    $role = Role::findOrCreate('editor', 'web');

    Livewire::test(Index::class)
        ->call('confirmDelete', (string) $role->id)
        ->call('delete');

    $this->assertDatabaseMissing('roles', ['id' => $role->id]);
});

test('super_admin role cannot be deleted', function () {
    asAdmin();

    $role = Role::findOrCreate(RoleEnum::SuperAdmin->value, 'web');

    Livewire::test(Index::class)
        ->call('confirmDelete', (string) $role->id)
        ->call('delete');

    $this->assertDatabaseHas('roles', ['id' => $role->id]);
});

test('non-admin cannot delete a role', function () {
    asUser();

    $role = Role::findOrCreate('editor', 'web');

    Livewire::test(Index::class)
        ->call('confirmDelete', (string) $role->id)
        ->call('delete')
        ->assertForbidden();
});
