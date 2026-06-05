<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rankings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->onDelete('cascade');
            $table->foreignId('participant_id')->constrained()->onDelete('cascade');

            $table->integer('rank')->default(0);         // Posisi ranking
            $table->integer('wins')->default(0);         // Jumlah menang
            $table->integer('losses')->default(0);       // Jumlah kalah
            $table->integer('points')->default(0);       // Total poin
            $table->integer('matches_played')->default(0); // Total match dimainkan

            $table->timestamps();

            // Satu peserta hanya punya 1 ranking per turnamen
            $table->unique(['tournament_id', 'participant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rankings');
    }
};