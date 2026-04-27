<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Participant;
use App\Models\Tournament;
use App\Models\User;

class TournamentSeeder extends Seeder
{
    public function run(): void
    {
        // Buat user admin
        User::firstOrCreate(
            ['email' => 'admin@tournament.com'],
            ['name' => 'Admin', 'password' => bcrypt('password123')]
        );

        // Buat 8 peserta contoh
        $participants = [
            ['name' => 'Budi Santoso',  'username' => 'budi123',   'email' => 'budi@test.com',   'game_id' => 'Budi#0001'],
            ['name' => 'Ani Wijaya',    'username' => 'ani456',    'email' => 'ani@test.com',    'game_id' => 'Ani#0002'],
            ['name' => 'Candra Putra',  'username' => 'candra789', 'email' => 'candra@test.com', 'game_id' => 'Candra#0003'],
            ['name' => 'Dewi Lestari',  'username' => 'dewi321',   'email' => 'dewi@test.com',   'game_id' => 'Dewi#0004'],
            ['name' => 'Eko Prasetyo',  'username' => 'eko654',    'email' => 'eko@test.com',    'game_id' => 'Eko#0005'],
            ['name' => 'Fitri Handoko', 'username' => 'fitri987',  'email' => 'fitri@test.com',  'game_id' => 'Fitri#0006'],
            ['name' => 'Galih Permana', 'username' => 'galih111',  'email' => 'galih@test.com',  'game_id' => 'Galih#0007'],
            ['name' => 'Hani Setiawan', 'username' => 'hani222',   'email' => 'hani@test.com',   'game_id' => 'Hani#0008'],
        ];

        foreach ($participants as $data) {
            Participant::firstOrCreate(
                ['email' => $data['email']],
                array_merge($data, ['status' => 'active'])
            );
        }

        // Buat turnamen contoh
        Tournament::firstOrCreate(
            ['name' => 'Mobile Legends Championship 2025'],
            [
                'game_name'        => 'Mobile Legends',
                'description'      => 'Turnamen ML antar kampus',
                'start_date'       => '2025-08-01',
                'end_date'         => '2025-08-31',
                'max_participants' => 8,
                'format'           => 'single_elimination',
                'status'           => 'upcoming',
                'prize_pool'       => 1000000,
            ]
        );

        $this->command->info('✅ Seeder berhasil! Login: admin@tournament.com / password123');
    }
}