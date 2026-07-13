<?php

namespace App\Enums;

/**
 * Kategori tetap untuk Artikel dan Media (Galeri). Nilai enum ini adalah
 * kategori berbahasa Indonesia (category_id) - satu-satunya sumber kebenaran.
 * category_en selalu diturunkan dari sini lewat en(), admin tidak memilih
 * category_en secara terpisah supaya tidak mungkin terjadi ketidakcocokan
 * (mis. category_id "Edukasi" tapi category_en "News").
 * Harus sinkron dengan migration
 * 2026_07_13_010848_add_category_en_and_rename_category_to_category_id_in_articles_and_media_table.
 */
enum ContentCategory: string
{
    case Umum = "Umum";
    case Edukasi = "Edukasi";
    case Akademik = "Akademik";
    case Berita = "Berita";
    case Pengumuman = "Pengumuman";
    case Kegiatan = "Kegiatan";

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public function en(): string
    {
        return match ($this) {
            self::Umum => "General",
            self::Edukasi => "Education",
            self::Akademik => "Academic",
            self::Berita => "News",
            self::Pengumuman => "Announcement",
            self::Kegiatan => "Event",
        };
    }
}
