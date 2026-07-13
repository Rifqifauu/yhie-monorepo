<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    private array $idValues = [
        "Umum",
        "Edukasi",
        "Akademik",
        "Berita",
        "Pengumuman",
        "Kegiatan",
    ];

    private array $enValues = [
        "General",
        "Education",
        "Academic",
        "News",
        "Announcement",
        "Event",
    ];

    /**
     * Pemetaan tetap ID -> EN, harus sinkron dengan App\Enums\ContentCategory::en().
     */
    private array $idToEnMap = [
        "Umum" => "General",
        "Edukasi" => "Education",
        "Akademik" => "Academic",
        "Berita" => "News",
        "Pengumuman" => "Announcement",
        "Kegiatan" => "Event",
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $idEnumList = "'" . implode("','", $this->idValues) . "'";
        $enEnumList = "'" . implode("','", $this->enValues) . "'";

        foreach (["articles", "media"] as $table) {
            // 1. Ganti nama kolom category -> category_id, nilai ENUM ID tidak berubah.
            DB::statement(
                "ALTER TABLE {$table} CHANGE category category_id ENUM($idEnumList) NOT NULL DEFAULT 'Umum'",
            );

            // 2. Tambah category_en, default sementara supaya kolom bisa NOT NULL.
            DB::statement(
                "ALTER TABLE {$table} ADD COLUMN category_en ENUM($enEnumList) NOT NULL DEFAULT 'General' AFTER category_id",
            );

            // 3. Backfill category_en berdasarkan category_id yang sudah ada di data lama.
            foreach ($this->idToEnMap as $id => $en) {
                DB::table($table)
                    ->where("category_id", $id)
                    ->update(["category_en" => $en]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $idEnumList = "'" . implode("','", $this->idValues) . "'";

        foreach (["articles", "media"] as $table) {
            DB::statement("ALTER TABLE {$table} DROP COLUMN category_en");
            DB::statement(
                "ALTER TABLE {$table} CHANGE category_id category ENUM($idEnumList) NOT NULL DEFAULT 'Umum'",
            );
        }
    }
};
