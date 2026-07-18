<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

it('logs an active user in', function () {
    $user = User::factory()->create(['password' => 'secret-password']);

    postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'secret-password',
    ])->assertOk()->assertJsonPath('data.email', $user->email);
});

it('returns the current authenticated user', function () {
    $user = User::factory()->create();
    actingAs($user);

    getJson('/api/auth/user')->assertOk()->assertJsonPath('data.email', $user->email);
});

it('logs an authenticated user out and invalidates access', function () {
    $user = User::factory()->create();
    actingAs($user);

    postJson('/api/auth/logout')->assertOk();
    getJson('/api/auth/user')->assertUnauthorized();
});

it('rejects inactive accounts at login', function () {
    $user = User::factory()->create([
        'password' => 'secret-password',
        'is_active' => false,
    ]);

    postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'secret-password',
    ])->assertUnprocessable();
});

it('ends access when an authenticated user becomes inactive', function () {
    $user = User::factory()->create();
    actingAs($user);
    $user->update(['is_active' => false]);

    getJson('/api/auth/user')->assertForbidden();
});

it('changes the authenticated users password safely', function () {
    $user = User::factory()->create(['password' => 'old-password']);
    actingAs($user);

    putJson('/api/auth/password', [
        'current_password' => 'old-password',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ])->assertOk();

    expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
});
