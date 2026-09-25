<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Specialty;

class SpecialtySeeder extends Seeder
{
    public function run(): void
    {
        Specialty::create([
            'name' => 'Penyakit Dalam',
            'description' => 'Menangani penyakit yang berkaitan dengan organ dalam tubuh.',
        ]);

        Specialty::create([
            'name' => 'Anak',
            'description' => 'Menangani kesehatan dan penyakit pada anak.',
        ]);

        Specialty::create([
            'name' => 'Gigi',
            'description' => 'Menangani kesehatan gigi dan mulut.',
        ]);

        Specialty::create([
            'name' => 'Mata',
            'description' => 'Menangani gangguan dan penyakit pada mata.',
        ]);
    }
}