<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

/** Data karangan yang masuk akal (bukan "aaa"), tersebar di rentang -6 sampai +7 hari. */
class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        if (Appointment::exists()) {
            return;
        }

        $pasien = User::where("role", "patient")->get();
        $dokter = Doctor::all();
        $ruang = Room::where("status", Room::AVAILABLE)->get();
        $slot = Appointment::slots();

        $keluhan = [
            "Demam dan batuk sejak tiga hari.",
            "Kontrol rutin tekanan darah.",
            "Gigi geraham kanan bawah terasa ngilu.",
            "Pandangan kabur saat membaca dekat.",
            "Anak sulit makan dan sering rewel.",
            "Nyeri ulu hati setelah makan.",
            "Pemeriksaan gigi dan pembersihan karang gigi.",
            "Mata merah dan berair sejak kemarin.",
        ];

        $dipakai = [];

        for ($i = 0; $i < 36; $i++) {
            $d = $dokter[$i % $dokter->count()];
            $offset = ($i % 14) - 6;
            $tanggal = now()->addDays($offset)->format("Y-m-d");
            $jam = $slot[($i * 3) % count($slot)];

            $kunci = "{$d->id}|{$tanggal}|{$jam}";
            if (isset($dipakai[$kunci])) {
                continue;
            }
            $dipakai[$kunci] = true;

            if ($offset < 0) {
                $status = $i % 5 === 0 ? Appointment::CANCELLED : Appointment::COMPLETED;
            } else {
                $status = $i % 3 === 0
                    ? Appointment::CONFIRMED
                    : ($i % 7 === 0 ? Appointment::CANCELLED : Appointment::PENDING);
            }

            $adaRuang = in_array($status, [Appointment::CONFIRMED, Appointment::COMPLETED], true);

            $pasien[$i % $pasien->count()]->appointments()->create([
                "doctor_id" => $d->id,
                "room_id" => $adaRuang ? $ruang[$i % $ruang->count()]->id : null,
                "appointment_date" => $tanggal,
                "appointment_time" => $jam,
                "status" => $status,
                "notes" => $keluhan[$i % count($keluhan)],
            ]);
        }
    }
}
