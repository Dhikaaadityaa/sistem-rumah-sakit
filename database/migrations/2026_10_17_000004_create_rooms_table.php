<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('rooms')) {
            Schema::create('rooms', function (Blueprint $table) {
                $table->id();
                $table->string('room_number', 10)->unique();
                $table->string('room_type', 30);
                $table->unsignedSmallInteger('floor');
                $table->string('status', 20)->default('available');
                $table->timestamps();
            });
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement(
                'CREATE UNIQUE INDEX IF NOT EXISTS rooms_room_number_unique
                 ON rooms (room_number)'
            );
        }
    }

    public function down(): void
    {
        // Pertahankan tabel dan data kamar yang sudah ada.
    }
};