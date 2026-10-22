<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('doctors')) {
            Schema::create('doctors', function (Blueprint $table) {
                $table->id();
                $table->foreignId('specialty_id')
                    ->constrained('specialties')
                    ->restrictOnDelete();
                $table->string('name', 120);
                $table->string('phone', 20)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Pertahankan tabel dan data dokter yang mungkin sudah digunakan.
    }
};