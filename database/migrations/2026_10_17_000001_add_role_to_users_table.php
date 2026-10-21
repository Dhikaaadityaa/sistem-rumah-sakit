<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table("users", function (Blueprint $table) {
            // "admin" atau "patient". Divalidasi di aplikasi, bukan enum database.
            $table->string("role", 20)->default("patient")->index();
        });
    }

    public function down(): void
    {
        Schema::table("users", function (Blueprint $table) {
            $table->dropColumn("role");
        });
    }
};
