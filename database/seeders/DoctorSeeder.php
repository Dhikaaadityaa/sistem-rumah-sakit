<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Specialty;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ["Penyakit Dalam", "dr. Andi Pratama, Sp.PD", "081234567801"],
            ["Penyakit Dalam", "dr. Budi Santoso, Sp.PD", "081234567802"],
            ["Anak", "dr. Citra Lestari, Sp.A", "081234567803"],
            ["Anak", "dr. Dimas Wijaya, Sp.A", "081234567804"],
            ["Gigi", "drg. Eka Putri", "081234567805"],
            ["Gigi", "drg. Fajar Ramadhan", "081234567806"],
            ["Mata", "dr. Galih Saputra, Sp.M", "081234567807"],
            ["Mata", "dr. Hani Permata, Sp.M", "081234567808"],
        ];

        foreach ($data as [$spesialisasi, $nama, $telepon]) {
            Doctor::updateOrCreate(
                ["name" => $nama],
                [
                    "specialty_id" => Specialty::where("name", $spesialisasi)->value("id"),
                    "phone" => $telepon,
                ]
            );
        }
    }
}
