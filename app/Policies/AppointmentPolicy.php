<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

/**
 * Policy = aturan untuk SATU model. Jawabannya bergantung pada
 * siapa penggunanya DAN janji temu mana yang dituju.
 */
class AppointmentPolicy
{
    /** index: semua pengguna masuk boleh membuka daftar (isinya disaring di kueri). */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /** show: admin boleh semua; pasien hanya miliknya. */
    public function view(User $user, Appointment $appointment): bool
    {
        return $user->isAdmin() || $appointment->user_id === $user->id;
    }

    /** create/store: hanya pasien yang mengajukan janji temu. */
    public function create(User $user): bool
    {
        return $user->isPatient();
    }

    /** edit/update: pemilik, dan hanya selama masih menunggu konfirmasi. */
    public function update(User $user, Appointment $appointment): bool
    {
        return $user->isPatient()
            && $appointment->user_id === $user->id
            && $appointment->status === Appointment::PENDING;
    }

    /** batal: pemilik atau admin, selama belum selesai/dibatalkan. */
    public function cancel(User $user, Appointment $appointment): bool
    {
        return in_array($appointment->status, [Appointment::PENDING, Appointment::CONFIRMED], true)
            && ($user->isAdmin() || $appointment->user_id === $user->id);
    }

    /** ubah status (konfirmasi/selesai) + tetapkan ruangan: hanya admin. */
    public function manage(User $user, Appointment $appointment): bool
    {
        return $user->isAdmin()
            && ! empty(Appointment::TRANSITIONS[$appointment->status]);
    }

    /** destroy: hanya admin. */
    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->isAdmin();
    }
}
