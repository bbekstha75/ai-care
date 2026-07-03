<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Call the artisan command to sync permissions dynamically
        Artisan::call('acl:sync-permissions');

        // Assign super_admin role to the seeded admin user.
        $adminUser = User::where('email', 'admin@docspe.com')->first();
        $adminUser?->assignRole(RoleEnum::SuperAdmin->value);
    }
}
