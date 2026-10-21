<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("appointments", function (Blueprint $table) {
            $table->id();
            // Riwayat janji temu tidak boleh hilang ikut penghapusan pasien/dokter: restrict.
            $table->foreignId("user_id")->constrained("users")->restrictOnDelete();
            $table->foreignId("doctor_id")->constrained("doctors")->restrictOnDelete();
            // Ruang pemeriksaan diisi admin saat janji dikonfirmasi.
            $table->foreignId("room_id")->nullable()->constrained("rooms")->nullOnDelete();

            $table->date("appointment_date");          // disimpan Y-m-d
            $table->time("appointment_time");
            $table->string("status", 20)->default("pending");
            $table->text("notes")->nullable();         // dienkripsi dua arah lewat cast "encrypted"

            $table->timestamps();

            $table->index(["status", "appointment_date"]);
        });

        // Pertahanan terakhir terhadap jadwal ganda (race condition): satu dokter,
        // satu tanggal, satu jam hanya boleh punya SATU janji yang belum dibatalkan.
        if (DB::getDriverName() === "pgsql") {
            DB::statement(
                "CREATE UNIQUE INDEX appointments_active_slot_unique
                 ON appointments (doctor_id, appointment_date, appointment_time)
                 WHERE status <> \x27cancelled\x27"
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists("appointments");
    }
};
