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
        Schema::table('transactions', function (Blueprint $table) {
            // Menambahkan kolom untuk menyimpan URL pembayaran DOKU
            $table->text('payment_url')->nullable()->after('pg_transaction_id');

            // Menambahkan kolom untuk mencatat waktu pembayaran sukses
            $table->timestamp('paid_at')->nullable()->after('payment_url');

            // Menambahkan kolom untuk menyimpan respon mentah dari webhook DOKU
            $table->json('pg_response')->nullable()->after('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'payment_url',
                'paid_at',
                'pg_response'
            ]);
        });
    }
};
