<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ApiManagementPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'services.view',
            'services.create',
            'services.edit',
            'services.delete',
            'api-keys.view',
            'api-keys.create',
            'api-keys.edit',
            'api-keys.delete',
            'api-logs.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $roleSuperadmin = Role::firstOrCreate(['name' => 'Super Admin']);

        foreach ($permissions as $permissionName) {
            if (!$roleSuperadmin->hasPermissionTo($permissionName)) {
                $roleSuperadmin->givePermissionTo($permissionName);
            }
        }
    }
}
