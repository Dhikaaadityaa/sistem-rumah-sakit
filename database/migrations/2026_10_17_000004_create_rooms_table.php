<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("rooms", function (Blueprint $table) {
            $table->id();
            $table->string("room_number", 10)->unique();
            $table->string("room_type", 30);
            $table->unsignedSmallInteger("floor");
            // available | occupied | maintenance (string biasa, divalidasi di Form Request)
            $table->string("status", 20)->default("available");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("rooms");
    }
};
