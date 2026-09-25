<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Rumah Sakit',
            'email' => 'admin@rumahsakit.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Pasien Contoh',
            'email' => 'pasien@rumahsakit.test',
            'password' => Hash::make('password'),
            'role' => 'patient',
        ]);
    }
}