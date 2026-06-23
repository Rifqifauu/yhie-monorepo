<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table("transactions", function (Blueprint $table) {
            // 1. Tambah reference_id setelah kolom ID registrasi
            $table
                ->string("reference_id")
                ->unique()
                ->after("program_registration_id");

            // 2. Tambah kolom-kolom baru lainnya di paling akhir (sebelum timestamps)
            $table
                ->string("payment_method")
                ->default("manual_transfer")
                ->after("payment_status");
            $table
                ->string("transaction_receipt")
                ->nullable()
                ->after("payment_method");
            $table
                ->string("pg_transaction_id")
                ->nullable()
                ->unique()
                ->after("transaction_receipt");

            // 3. Modifikasi ENUM payment_status untuk menambah opsi 'expired'
            // CATATAN: Di Laravel lama butuh package doctrine/dbal, tapi di Laravel modern (v10/v11) bisa langsung dicoding seperti ini:
            $table
                ->enum("payment_status", [
                    "pending",
                    "completed",
                    "failed",
                    "expired",
                ])
                ->default("pending")
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("transactions", function (Blueprint $table) {
            // Kembalikan enum ke struktur awal jika di-rollback
            $table
                ->enum("payment_status", ["pending", "completed", "failed"])
                ->default("pending")
                ->change();

            // Hapus kolom yang tadi ditambahkan
            $table->dropColumn([
                "reference_id",
                "payment_method",
                "transaction_receipt",
                "pg_transaction_id",
            ]);
        });
    }
};
