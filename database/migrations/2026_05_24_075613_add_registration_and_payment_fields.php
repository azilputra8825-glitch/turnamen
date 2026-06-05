<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah entry_fee di tabel tournaments
        Schema::table('tournaments', function (Blueprint $table) {
            $table->decimal('entry_fee', 10, 2)->default(0.00)->after('prize_pool');
        });

        // 2. Tambah user_id di tabel participants
        Schema::table('participants', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
        });

        // 3. Tambah kolom pembayaran di tabel pivot tournament_participant
        Schema::table('tournament_participant', function (Blueprint $table) {
            $table->enum('payment_status', ['unpaid', 'pending', 'verified', 'rejected'])->default('unpaid')->after('status');
            $table->string('payment_proof')->nullable()->after('payment_status');
            $table->decimal('amount_paid', 10, 2)->nullable()->after('payment_proof');
            $table->string('rejection_reason')->nullable()->after('amount_paid');
        });
    }

    public function down(): void
    {
        Schema::table('tournament_participant', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'payment_proof', 'amount_paid', 'rejection_reason']);
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->dropForeign(['participants_user_id_foreign']);
            $table->dropColumn('user_id');
        });

        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropColumn('entry_fee');
        });
    }
};
