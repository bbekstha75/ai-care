<?php

use App\Enums\RoleEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
*/

pest()->extend(TestCase::class)
 // ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
*/

/**
 * Create a user with the super_admin role and set them as the acting user.
 */
function asAdmin(): User
{
    Role::findOrCreate(RoleEnum::SuperAdmin->value, 'web');

    $user = User::factory()->create();
    $user->assignRole(RoleEnum::SuperAdmin->value);

    test()->actingAs($user);

    return $user;
}

/**
 * Create a user with the user role and set them as the acting user.
 */
function asUser(): User
{
    Role::findOrCreate(RoleEnum::User->value, 'web');

    $user = User::factory()->create();
    $user->assignRole(RoleEnum::User->value);

    test()->actingAs($user);

    return $user;
}
