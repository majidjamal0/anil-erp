<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        $email = env('SUPER_ADMIN_EMAIL');
        $password = env('SUPER_ADMIN_PASSWORD');
        $hasCredentials = filled($email) || filled($password);

        if (app()->environment(['local', 'development']) && ! $hasCredentials) {
            throw new RuntimeException(
                'SUPER_ADMIN_EMAIL and SUPER_ADMIN_PASSWORD are required for local seeding.'
            );
        }

        if (! $hasCredentials) {
            return;
        }

        if (blank($email) || blank($password)) {
            throw new RuntimeException(
                'Both SUPER_ADMIN_EMAIL and SUPER_ADMIN_PASSWORD must be configured.'
            );
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Super Admin',
                'password' => $password,
                'is_active' => true,
            ]
        );

        $user->assignRole('Super Admin');
    }
}
