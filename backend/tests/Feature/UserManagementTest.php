<?php

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

it('allows super admin to perform user crud', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');
    actingAs($admin);

    $response = postJson('/api/users', [
        'name' => 'Sales User',
        'email' => 'sales@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'roles' => ['Salesperson'],
    ])->assertCreated()->assertJsonPath('data.roles.0', 'Salesperson');

    $userId = $response->json('data.id');
    getJson("/api/users/{$userId}")->assertOk();
    putJson("/api/users/{$userId}", [
        'name' => 'Updated User',
        'email' => 'sales@example.com',
        'is_active' => false,
    ])->assertOk()->assertJsonPath('data.is_active', false);
    deleteJson("/api/users/{$userId}")->assertNoContent();
});

it('assigns and removes roles', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');
    $user = User::factory()->create();
    actingAs($admin);

    postJson("/api/users/{$user->id}/roles", ['role' => 'Accountant'])
        ->assertOk();
    expect($user->fresh()->hasRole('Accountant'))->toBeTrue();

    deleteJson("/api/users/{$user->id}/roles/Accountant")->assertOk();
    expect($user->fresh()->hasRole('Accountant'))->toBeFalse();
});

it('protects the super admin role and users', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');
    actingAs($admin);
    $role = Role::findByName('Super Admin');

    putJson("/api/roles/{$role->id}", ['name' => 'Renamed'])
        ->assertUnprocessable();
    deleteJson("/api/roles/{$role->id}")->assertUnprocessable();
    deleteJson("/api/users/{$admin->id}/roles/Super Admin")->assertUnprocessable();
});

it('forbids users without permission from managing users', function () {
    actingAs(User::factory()->create());

    getJson('/api/users')->assertForbidden();
    postJson('/api/users', [])->assertForbidden();
});
