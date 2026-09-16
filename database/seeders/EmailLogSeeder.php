<?php

namespace Database\Seeders;

use App\Models\SsoProvider;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class EmailLogSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'email-logs.view',
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
