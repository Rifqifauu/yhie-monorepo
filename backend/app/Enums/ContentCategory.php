<?php

namespace App\Enums;

/**
 * Kategori tetap untuk Artikel dan Media (Galeri).
 * Harus sinkron dengan ENUM kolom `category` di migration
 * 2026_07_10_000001_convert_category_to_enum_in_articles_and_media_table.
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
}
