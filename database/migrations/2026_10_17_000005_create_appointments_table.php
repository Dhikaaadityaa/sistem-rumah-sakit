<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('appointments')) {
            Schema::create('appointments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')
                    ->constrained('users')
                    ->restrictOnDelete();
                $table->foreignId('doctor_id')
                    ->constrained('doctors')
                    ->restrictOnDelete();
                $table->foreignId('room_id')
                    ->nullable()
                    ->constrained('rooms')
                    ->nullOnDelete();
                $table->date('appointment_date');
                $table->time('appointment_time');
                $table->string('status', 20)->default('pending');
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->index(['status', 'appointment_date']);
            });
        } else {
            if (!Schema::hasColumn('appointments', 'room_id')) {
                Schema::table('appointments', function (Blueprint $table) {
                    $table->foreignId('room_id')
                        ->nullable()
                        ->constrained('rooms')
                        ->nullOnDelete();
                });
            }

            if (!Schema::hasIndex('appointments', 'appointments_status_appointment_date_index')) {
                Schema::table('appointments', function (Blueprint $table) {
                    $table->index(['status', 'appointment_date']);
                });
            }
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement(
                "CREATE UNIQUE INDEX IF NOT EXISTS appointments_active_slot_unique
                 ON appointments (doctor_id, appointment_date, appointment_time)
                 WHERE status <> 'cancelled'"
            );
        }
    }

    public function down(): void
    {
        // Pertahankan tabel, relasi, indeks, dan data janji temu yang sudah ada.
    }
};