<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        Room::create([
            'room_number' => '101',
            'room_type' => 'VIP',
            'floor' => 1,
            'status' => 'available',
        ]);

        Room::create([
            'room_number' => '102',
            'room_type' => 'Kelas 1',
            'floor' => 1,
            'status' => 'occupied',
        ]);

        Room::create([
            'room_number' => '103',
            'room_type' => 'Kelas 2',
            'floor' => 1,
            'status' => 'available',
        ]);

        Room::create([
            'room_number' => '201',
            'room_type' => 'VIP',
            'floor' => 2,
            'status' => 'available',
        ]);

        Room::create([
            'room_number' => '202',
            'room_type' => 'Kelas 1',
            'floor' => 2,
            'status' => 'occupied',
        ]);

        Room::create([
            'room_number' => '203',
            'room_type' => 'ICU',
            'floor' => 2,
            'status' => 'maintenance',
        ]);

        Room::create([
            'room_number' => '301',
            'room_type' => 'Kelas 2',
            'floor' => 3,
            'status' => 'available',
        ]);

        Room::create([
            'room_number' => '302',
            'room_type' => 'ICU',
            'floor' => 3,
            'status' => 'available',
        ]);
    }
}