<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (! $email && ! $password) {
            $this->command?->warn('ADMIN_EMAIL and ADMIN_PASSWORD are unset; no admin user was created.');

            return;
        }

        if (! $email || ! $password || mb_strlen($password) < 12) {
            throw new RuntimeException('Set ADMIN_EMAIL and an ADMIN_PASSWORD of at least 12 characters before seeding an admin.');
        }

        User::updateOrCreate(
            ['email' => $email],
            ['name' => env('ADMIN_NAME', 'BUKTRAVELS Administrator'), 'password' => Hash::make($password), 'role' => 'admin']
        );
    }
}
