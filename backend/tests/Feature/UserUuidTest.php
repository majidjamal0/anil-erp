<?php

use App\Models\User;
use Illuminate\Support\Str;

it('assigns a UUID to a new user', function (): void {
    $user = User::factory()->create();

    expect(Str::isUuid($user->id))->toBeTrue()
        ->and($user->locale)->toBe('fa');
});
