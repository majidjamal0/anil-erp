<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;

it('uses UUIDs throughout role and permission assignments', function (): void {
    $permission = Permission::create(['name' => 'inventory.view', 'guard_name' => 'web']);
    $role = Role::create(['name' => 'warehouse-manager', 'guard_name' => 'web']);
    $user = User::factory()->create();

    $role->givePermissionTo($permission);
    $user->assignRole($role);

    expect(Str::isUuid($role->id))->toBeTrue()
        ->and(Str::isUuid($permission->id))->toBeTrue()
        ->and($user->hasPermissionTo('inventory.view'))->toBeTrue();
});
