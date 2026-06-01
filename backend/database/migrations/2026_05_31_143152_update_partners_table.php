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
        Schema::table('partners', function (Blueprint $table) {
            // 1. Rename kolom lama menjadi bahasa Indonesia agar data lama tidak hilang
            $table->renameColumn('name', 'name_id');
            $table->renameColumn('description', 'description_id');
            $table->renameColumn('slug', 'slug_id');
        });

        Schema::table('partners', function (Blueprint $table) {
            // 2. Tambahkan kolom baru untuk bahasa Inggris
            // Menggunakan nullable() opsional jika kamu tidak ingin data lama error karena kolom ini kosong
            $table->string('name_en')->after('name_id')->nullable();
            $table->string('description_en')->after('description_id')->nullable();
            $table->string('slug_en')->after('slug_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            // 1. Hapus kolom bahasa Inggris
            $table->dropColumn(['name_en', 'description_en', 'slug_en']);
        });

        Schema::table('partners', function (Blueprint $table) {
            // 2. Kembalikan nama kolom ke versi aslinya
            $table->renameColumn('name_id', 'name');
            $table->renameColumn('description_id', 'description');
            $table->renameColumn('slug_id', 'slug');
        });
    }
};