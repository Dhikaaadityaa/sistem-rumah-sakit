<?php

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Room;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        $user = request()->user();

        $appointments = Appointment::query()
            ->visibleTo($user);

        return Inertia::render('dashboard', [
            'stats' => [
                'appointments' => (clone $appointments)->count(),
                'pending' => (clone $appointments)
                    ->where('status', Appointment::PENDING)->count(),
                'confirmed' => (clone $appointments)
                    ->where('status', Appointment::CONFIRMED)->count(),
                'completed' => (clone $appointments)
                    ->where('status', Appointment::COMPLETED)->count(),
                'cancelled' => (clone $appointments)
                    ->where('status', Appointment::CANCELLED)->count(),
                'doctors' => Doctor::count(),
                'rooms' => Room::count(),
                'availableRooms' => Room::where(
                    'status',
                    Room::AVAILABLE
                )->count(),
                'specialties' => Specialty::count(),
                'patients' => User::where(
                    'role',
                    User::ROLE_PATIENT
                )->count(),
            ],
            'recentAppointments' => (clone $appointments)
                ->with([
                    'user:id,name',
                    'doctor:id,name',
                    'room:id,room_number',
                ])
                ->latest('created_at')
                ->limit(5)
                ->get(),
            'isAdmin' => $user->isAdmin(),
        ]);
    })->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/hospital.php';