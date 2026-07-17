<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'roles.manage',
            'permissions.manage',
            'branches.manage',
            'warehouses.manage',
            'sales.manage',
            'accounting.manage',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $roles = [
            'Super Admin' => $permissions,
            'Administrator' => array_slice($permissions, 0, 8),
            'Branch Manager' => ['users.view', 'branches.manage', 'sales.manage'],
            'Warehouse Manager' => ['users.view', 'warehouses.manage'],
            'Salesperson' => ['sales.manage'],
            'Accountant' => ['accounting.manage'],
        ];

        foreach ($roles as $name => $grants) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web'])
                ->syncPermissions($grants);
        }
    }
}
