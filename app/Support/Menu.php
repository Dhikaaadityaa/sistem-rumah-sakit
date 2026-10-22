<?php

namespace App\Support;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Room;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;

/**
 * Menu dibangun di SERVER dari Gate/Policy yang SAMA dengan yang menjaga aksinya.
 * React hanya menggambar apa yang dikirim. Menyembunyikan menu = kesopanan;
 * penegakan tetap oleh middleware + Policy.
 */
class Menu
{
    public static function for(User $user): array
    {
        $items = [
            ["title" => "Dasbor", "href" => route("dashboard")],
            ["title" => "Rekap", "href" => route("recap"), "ability" => ["view-recap"]],
            ["title" => "Janji Temu", "href" => route("appointments.index"), "ability" => ["viewAny", Appointment::class]],
            ["title" => "Dokter", "href" => route("doctors.index"), "ability" => ["viewAny", Doctor::class]],
            ["title" => "Ruangan", "href" => route("rooms.index"), "ability" => ["viewAny", Room::class]],
            ["title" => "Spesialisasi", "href" => route("specialties.index"), "ability" => ["viewAny", Specialty::class]],
        ];

        return collect($items)
            ->filter(function (array $item) use ($user) {
                if (! isset($item["ability"])) {
                    return true;
                }

                return Gate::forUser($user)->allows($item["ability"][0], $item["ability"][1] ?? []);
            })
            ->map(fn (array $item) => Arr::only($item, ["title", "href"]))
            ->values()
            ->all();
    }
}
