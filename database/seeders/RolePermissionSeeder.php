<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $roleMember = Role::firstOrCreate(['name' => 'Member']);

        $roleSuperadmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $roleSuperadmin->syncPermissions(Permission::all());

        // give role to user
        $user = User::where('username', 'superadmin')->first();
        if ($user) {
            $user->assignRole($roleSuperadmin);
        }
    }
}
