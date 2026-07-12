<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Daftar kategori tetap - disepakati bersama, mencakup kategori yang sudah
     * dipakai di data live + kategori yang direncanakan untuk konten ke depan.
     */
    private array $categories = [
        "Umum",
        "Edukasi",
        "Akademik",
        "Berita",
        "Pengumuman",
        "Kegiatan",
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Rapikan dulu data lama (beda kapitalisasi / nilai di luar daftar)
        // supaya tidak ada yang tertolak/hilang saat kolom dikunci ke ENUM.
        DB::table("articles")
            ->whereRaw("LOWER(category) = ?", ["umum"])
            ->update(["category" => "Umum"]);
        DB::table("articles")
            ->whereRaw("LOWER(category) = ?", ["edukasi"])
            ->update(["category" => "Edukasi"]);
        DB::table("articles")
            ->whereNotIn("category", $this->categories)
            ->update(["category" => "Umum"]);

        DB::table("media")
            ->whereRaw("LOWER(category) = ?", ["edukasi"])
            ->update(["category" => "Edukasi"]);
        DB::table("media")
            ->whereRaw("LOWER(category) = ?", ["akademik"])
            ->update(["category" => "Akademik"]);
        DB::table("media")
            ->where(function ($q) {
                $q->whereNull("category")->orWhereNotIn(
                    "category",
                    $this->categories,
                );
            })
            ->update(["category" => "Umum"]);

        $enumList = "'" . implode("','", $this->categories) . "'";
        DB::statement(
            "ALTER TABLE articles MODIFY category ENUM($enumList) NOT NULL DEFAULT 'Umum'",
        );
        DB::statement(
            "ALTER TABLE media MODIFY category ENUM($enumList) NOT NULL DEFAULT 'Umum'",
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE articles MODIFY category VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE media MODIFY category VARCHAR(255) NULL");
    }
};
