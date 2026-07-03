<?php

namespace App\Console\Commands;

use App\Enums\RoleEnum;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Console\Command;
use Spatie\Permission\PermissionRegistrar;

class SyncPermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acl:sync-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and sync permissions from config/acl.php to the super_admin role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting permission synchronization...');

        // Flush permission cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $modules = config('acl.modules', []);
        $customPermissions = config('acl.custom_permissions', []);

        $actions = ['list', 'view', 'store', 'update', 'delete'];
        $generatedPermissions = [];

        // Generate standard permissions for modules
        foreach ($modules as $module) {
            foreach ($actions as $action) {
                $permissionName = "{$module} {$action}";
                $generatedPermissions[] = $permissionName;
                Permission::findOrCreate($permissionName, 'web');
            }
        }

        // Generate custom permissions
        foreach ($customPermissions as $permissionName) {
            $generatedPermissions[] = $permissionName;
            Permission::findOrCreate($permissionName, 'web');
        }

        $this->info('Created '.count($generatedPermissions).' permissions.');

        // Get or create super_admin role
        $superAdminRole = Role::findOrCreate(RoleEnum::SuperAdmin->value, 'web');

        // Force cache clear before syncing just in case
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Sync permissions to super_admin role
        $superAdminRole->syncPermissions($generatedPermissions);
        $this->info('Synced all permissions to the super_admin role.');

        // Optionally, remove old permissions that are no longer in the config
        $stalePermissions = Permission::whereNotIn('name', $generatedPermissions)->get();
        if ($stalePermissions->count() > 0) {
            $this->warn('Found '.$stalePermissions->count().' stale permissions. Deleting them...');
            foreach ($stalePermissions as $stalePermission) {
                $stalePermission->delete();
            }
            $this->info('Stale permissions deleted.');
        }

        $this->info('Permission synchronization complete.');
    }
}
