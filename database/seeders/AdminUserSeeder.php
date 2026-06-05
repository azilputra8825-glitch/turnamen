<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin utama
        User::updateOrCreate(
            ['email' => 'admin@turnamen.com'],
            [
                'name'     => 'Administrator',
                'email'    => 'admin@turnamen.com',
                'password' => Hash::make('password123'),
                'role'     => 'admin',
            ]
        );

        // Akun viewer untuk demo
        User::updateOrCreate(
            ['email' => 'viewer@turnamen.com'],
            [
                'name'     => 'Viewer Demo',
                'email'    => 'viewer@turnamen.com',
                'password' => Hash::make('password123'),
                'role'     => 'viewer',
            ]
        );
    }
}
