<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\RecapController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SpecialtyController;
use Illuminate\Support\Facades\Route;

/*
| Dimuat dari routes/web.php dengan:  require __DIR__."/hospital.php";
|
| Pertanyaan wajib untuk setiap rute baru: SIAPA yang boleh membukanya?
| Semua rute di bawah berada di dalam kelompok middleware, tidak ada yang terbuka.
| Lapisan 1 = middleware (auth / role). Lapisan 2 = Policy/Gate di controller dan Form Request.
*/

Route::middleware(["auth", "verified"])->group(function () {

    // ---- Khusus admin: DIDAFTARKAN LEBIH DULU supaya /doctors/create tidak ----
    // ---- tertangkap rute /doctors/{doctor} milik semua pengguna di bawah. ----
    Route::middleware("role:admin")->group(function () {
        Route::get("recap", RecapController::class)->name("recap");

        Route::resource("specialties", SpecialtyController::class)->except("show");
        Route::resource("doctors", DoctorController::class)->except(["index", "show"]);
        Route::resource("rooms", RoomController::class)->except(["index", "show"]);

        Route::patch("appointments/{appointment}/status", [AppointmentController::class, "updateStatus"])
            ->name("appointments.status");
        Route::delete("appointments/{appointment}", [AppointmentController::class, "destroy"])
            ->name("appointments.destroy");
    });

    // ---- Semua pengguna yang sudah masuk (data disaring oleh Policy/kueri) ----
    Route::resource("doctors", DoctorController::class)->only(["index", "show"]);
    Route::resource("rooms", RoomController::class)->only("index");

    Route::patch("appointments/{appointment}/cancel", [AppointmentController::class, "cancel"])
        ->name("appointments.cancel");
    Route::resource("appointments", AppointmentController::class)->except("destroy");
});
