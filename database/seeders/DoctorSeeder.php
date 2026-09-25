<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Doctor;
use App\Models\Specialty;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        Doctor::create([
            'specialty_id' => Specialty::where('name', 'Penyakit Dalam')->first()->id,
            'name' => 'Dr. Andi Pratama',
            'phone' => '081234567801',
        ]);

        Doctor::create([
            'specialty_id' => Specialty::where('name', 'Penyakit Dalam')->first()->id,
            'name' => 'Dr. Budi Santoso',
            'phone' => '081234567802',
        ]);

        Doctor::create([
            'specialty_id' => Specialty::where('name', 'Anak')->first()->id,
            'name' => 'Dr. Citra Lestari',
            'phone' => '081234567803',
        ]);

        Doctor::create([
            'specialty_id' => Specialty::where('name', 'Anak')->first()->id,
            'name' => 'Dr. Dimas Wijaya',
            'phone' => '081234567804',
        ]);

        Doctor::create([
            'specialty_id' => Specialty::where('name', 'Gigi')->first()->id,
            'name' => 'Dr. Eka Putri',
            'phone' => '081234567805',
        ]);

        Doctor::create([
            'specialty_id' => Specialty::where('name', 'Gigi')->first()->id,
            'name' => 'Dr. Fajar Ramadhan',
            'phone' => '081234567806',
        ]);

        Doctor::create([
            'specialty_id' => Specialty::where('name', 'Mata')->first()->id,
            'name' => 'Dr. Galih Saputra',
            'phone' => '081234567807',
        ]);

        Doctor::create([
            'specialty_id' => Specialty::where('name', 'Mata')->first()->id,
            'name' => 'Dr. Hani Permata',
            'phone' => '081234567808',
        ]);
    }
}