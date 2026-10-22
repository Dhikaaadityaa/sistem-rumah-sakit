<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Room;
use App\Models\Specialty;
use App\Support\Tanggal;
use Illuminate\Support\Facades\DB;

class RecapService
{
    public function build(): array
    {
        $perStatus = Appointment::select("status", DB::raw("count(*) as total"))
            ->groupBy("status")
            ->pluck("total", "status");

        $perRuangan = Room::select("status", DB::raw("count(*) as total"))
            ->groupBy("status")
            ->pluck("total", "status");

        return [
            "total_janji" => (int) $perStatus->sum(),

            "per_status" => collect(Appointment::STATUSES)->map(fn ($s) => [
                "status" => $s,
                "label" => Appointment::LABELS[$s],
                "total" => (int) ($perStatus[$s] ?? 0),
            ])->all(),

            "per_bulan" => Appointment::selectRaw(
                "TO_CHAR(appointment_date, 'YYYY-MM') as bulan, count(*) as total"
            )
                ->where(
                    "appointment_date",
                    ">=",
                    now()->subMonths(5)->startOfMonth()->format("Y-m-d")
                )
                ->groupBy("bulan")
                ->orderBy("bulan")
                ->get()
                ->map(fn ($r) => [
                    "bulan" => $r->bulan,
                    "label" => Tanggal::bulanTahun($r->bulan),
                    "total" => (int) $r->total,
                ])->all(),

            "per_spesialisasi" => Specialty::select(
                "specialties.name",
                DB::raw("count(appointments.id) as total")
            )
                ->leftJoin("doctors", "doctors.specialty_id", "=", "specialties.id")
                ->leftJoin("appointments", "appointments.doctor_id", "=", "doctors.id")
                ->groupBy("specialties.id", "specialties.name")
                ->orderByDesc("total")
                ->orderBy("specialties.name")
                ->get()
                ->map(fn ($r) => [
                    "name" => $r->name,
                    "total" => (int) $r->total,
                ])->all(),

            "ruangan" => collect(Room::STATUSES)->map(fn ($s) => [
                "status" => $s,
                "label" => Room::LABELS[$s],
                "total" => (int) ($perRuangan[$s] ?? 0),
            ])->all(),

            "hari_ini" => Appointment::with(["doctor", "user:id,name", "room"])
                ->whereDate("appointment_date", today())
                ->where("status", "!=", Appointment::CANCELLED)
                ->orderBy("appointment_time")
                ->limit(8)
                ->get(),
        ];
    }
}
