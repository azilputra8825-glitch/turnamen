<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // Nama peserta
            $table->string('username')->unique();      // Username unik
            $table->string('email')->unique();         // Email unik
            $table->string('phone')->nullable();       // No. HP (opsional)
            $table->string('game_id')->nullable();     // ID dalam game (misal: RiotID)
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};