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
            'companies.view','companies.create','companies.update','companies.delete',
            'branches.view','branches.create','branches.update','branches.delete',
            'warehouses.view','warehouses.create','warehouses.update','warehouses.delete',
            'warehouse_types.manage',
            'sales_channels.view','sales_channels.create','sales_channels.update','sales_channels.delete',
            'organization.assign_access',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $roles = [
            'Super Admin' => $permissions,
            'Administrator' => array_slice($permissions, 0, 8),
            'Branch Manager' => ['users.view', 'branches.view', 'branches.update', 'warehouses.view', 'sales_channels.view', 'sales.manage'],
            'Warehouse Manager' => ['users.view', 'warehouses.view', 'warehouses.update'],
            'Salesperson' => ['branches.view', 'warehouses.view', 'sales_channels.view', 'sales.manage'],
            'Accountant' => ['companies.view', 'branches.view', 'warehouses.view', 'sales_channels.view', 'accounting.manage'],
        ];

        foreach ($roles as $name => $grants) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web'])
                ->syncPermissions($grants);
        }
    }
}
