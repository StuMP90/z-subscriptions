<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_USER_EMAIL');
        $password = env('ADMIN_USER_PASSWORD');

        if (! $email || ! $password) {
            return;
        }

        if (! User::where('email', $email)->exists()) {
            User::forceCreate([
                'name' => 'Admin User',
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
                'is_active' => true,
            ]);
        }
    }
}
