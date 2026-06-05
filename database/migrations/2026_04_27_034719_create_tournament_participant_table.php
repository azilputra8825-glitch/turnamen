<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel ini menghubungkan peserta dengan turnamen (Many-to-Many)
        Schema::create('tournament_participant', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->onDelete('cascade');
            $table->foreignId('participant_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['registered', 'confirmed', 'disqualified'])
                  ->default('registered');
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamps();

            // Satu peserta hanya bisa daftar sekali per turnamen
            $table->unique(['tournament_id', 'participant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournament_participant');
    }
};