<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $akun = [
            ["Admin Rumah Sakit", "admin@rumahsakit.test", "admin"],
            ["Siti Rahmawati", "pasien@rumahsakit.test", "patient"],
            ["Budi Hartono", "budi@rumahsakit.test", "patient"],
            ["Ani Lestari", "ani@rumahsakit.test", "patient"],
        ];

        foreach ($akun as [$nama, $email, $peran]) {
            // forceFill: "role" memang tidak fillable (mencegah mass assignment).
            User::firstOrNew(["email" => $email])->forceFill([
                "name" => $nama,
                "password" => Hash::make("password"),   // hash SATU ARAH (bcrypt), tidak bisa dikembalikan
                "role" => $peran,
                "email_verified_at" => now(),
            ])->save();
        }
    }
}
