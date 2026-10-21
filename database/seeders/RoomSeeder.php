<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ["101", "Poli Umum", 1, "available"],
            ["102", "Poli Umum", 1, "occupied"],
            ["103", "Poli Anak", 1, "available"],
            ["104", "Poli Gigi", 1, "available"],
            ["201", "Poli Mata", 2, "available"],
            ["202", "Poli Penyakit Dalam", 2, "available"],
            ["203", "Ruang Tindakan", 2, "maintenance"],
            ["301", "Poli Penyakit Dalam", 3, "available"],
        ];

        foreach ($data as [$nomor, $tipe, $lantai, $status]) {
            Room::updateOrCreate(
                ["room_number" => $nomor],
                ["room_type" => $tipe, "floor" => $lantai, "status" => $status]
            );
        }
    }
}
