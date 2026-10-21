<?php

namespace Database\Seeders;

use App\Models\Specialty;
use Illuminate\Database\Seeder;

class SpecialtySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ["Penyakit Dalam", "Menangani penyakit yang berkaitan dengan organ dalam tubuh."],
            ["Anak", "Menangani kesehatan dan penyakit pada anak."],
            ["Gigi", "Menangani kesehatan gigi dan mulut."],
            ["Mata", "Menangani gangguan dan penyakit pada mata."],
        ];

        foreach ($data as [$nama, $deskripsi]) {
            Specialty::updateOrCreate(["name" => $nama], ["description" => $deskripsi]);
        }
    }
}
