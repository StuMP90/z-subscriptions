<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (! User::where('email', 'admin@zsubscriptions.local')->exists()) {
            User::forceCreate([
                'name' => 'Admin User',
                'email' => 'admin@zsubscriptions.local',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]);
        }
    }
}
