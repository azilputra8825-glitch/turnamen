<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->onDelete('cascade');

            // Peserta 1 dan Peserta 2 yang bertanding
            $table->foreignId('participant1_id')->constrained('participants')->onDelete('cascade');
            $table->foreignId('participant2_id')->constrained('participants')->onDelete('cascade');

            // Pemenang (null = belum ada pemenang)
            $table->foreignId('winner_id')->nullable()->constrained('participants')->onDelete('set null');

            $table->integer('round');              // Ronde ke berapa
            $table->integer('match_number');       // Nomor pertandingan
            $table->datetime('scheduled_at')->nullable(); // Jadwal pertandingan
            $table->datetime('played_at')->nullable();    // Kapan dimainkan

            // Skor masing-masing peserta
            $table->integer('score_participant1')->default(0);
            $table->integer('score_participant2')->default(0);

            $table->enum('status', [
                'scheduled',   // sudah dijadwalkan
                'ongoing',     // sedang berlangsung
                'completed'    // selesai
            ])->default('scheduled');

            $table->text('notes')->nullable(); // Catatan tambahan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};