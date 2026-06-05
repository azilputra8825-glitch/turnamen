<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus foreign key lama, jadikan nullable, lalu tambahkan kembali
        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeign(['participant1_id']);
            $table->dropForeign(['participant2_id']);
        });

        // Jadikan nullable via Schema builder (SQLite and MySQL compatible)
        Schema::table('matches', function (Blueprint $table) {
            $table->unsignedBigInteger('participant1_id')->nullable()->change();
            $table->unsignedBigInteger('participant2_id')->nullable()->change();
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->foreign('participant1_id')->references('id')->on('participants')->onDelete('set null');
            $table->foreign('participant2_id')->references('id')->on('participants')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeign(['participant1_id']);
            $table->dropForeign(['participant2_id']);
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->unsignedBigInteger('participant1_id')->change();
            $table->unsignedBigInteger('participant2_id')->change();
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->foreign('participant1_id')->references('id')->on('participants')->onDelete('cascade');
            $table->foreign('participant2_id')->references('id')->on('participants')->onDelete('cascade');
        });
    }
};
