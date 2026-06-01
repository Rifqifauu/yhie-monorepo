<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Contoh data media
        $mediaItems = [];
        for ($i = 1; $i <= 50; $i++) {
            $mediaItems[] = [
                'title_id' => "Media {$i}",
                'description_id' => "Deskripsi untuk Media {$i} dalam bahasa Indonesia.",
                'title_en' => "Media {$i}",
                'slug_id' => "media-{$i}",
                'slug_en' => "media-{$i}",
                'description_en' => "Description for Media {$i} in English.",
                'image' => json_encode([
                    'path' => "path/to/image{$i}.png",
                ]),
            ];
        }

        // Insert data ke tabel media
        foreach ($mediaItems as $media) {
            \DB::table('media')->insert($media);
        }
    }
}
