<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name');                   // Nama turnamen
            $table->string('game_name');              // Nama game (Mobile Legends, dll)
            $table->text('description')->nullable();  // Deskripsi turnamen
            $table->date('start_date');               // Tanggal mulai
            $table->date('end_date');                 // Tanggal selesai
            $table->integer('max_participants');      // Maks peserta
            $table->enum('format', [
                'single_elimination',   // kalah = gugur
                'double_elimination',   // 2x kalah baru gugur
                'round_robin'           // semua lawan semua
            ])->default('single_elimination');
            $table->enum('status', [
                'upcoming',    // belum mulai
                'ongoing',     // sedang berjalan
                'completed'    // selesai
            ])->default('upcoming');
            $table->decimal('prize_pool', 10, 2)->nullable(); // Total hadiah
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};