<?php

namespace App\Policies;

use App\Models\Specialty;
use App\Models\User;

/** Pengelolaan spesialisasi: seluruhnya hanya untuk admin. */
class SpecialtyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Specialty $specialty): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Specialty $specialty): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Specialty $specialty): bool
    {
        return $user->isAdmin();
    }
}
