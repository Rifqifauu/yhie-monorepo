<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Carbon\Carbon;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat dua Faker untuk dua bahasa yang berbeda
        $fakerId = Faker::create('id_ID'); // Bahasa Indonesia
        $fakerEn = Faker::create('en_US'); // Bahasa Inggris

        $articles = [];
        $now = Carbon::now();

        // Looping untuk membuat 30 data artikel
        for ($i = 1; $i <= 30; $i++) {
            $titleId = $fakerId->sentence();
            $titleEn = $fakerEn->sentence();

            $articles[] = [
                'title_id' => $titleId,
                'title_en' => $titleEn,
                'content_id' => $fakerId->paragraphs(4, true),
                'content_en' => $fakerEn->paragraphs(4, true),
                'slug_id' => Str::slug($titleId) . '-' . $i,
                'slug_en' => Str::slug($titleEn) . '-' . $i,

                'image' => json_encode([
                    [
                        'https://images.unsplash.com/photo-1542810634-71277d95dcbb?q=80&w=600&auto=format&fit=crop',
                        'https://images.unsplash.com/photo-1509062522246-3755977927d7?q=80&w=600&auto=format&fit=crop',
                        'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?q=80&w=600&auto=format&fit=crop',
                        'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=600&auto=format&fit=crop'
                    ][$i % 4]
                ]),

                'is_published' => $fakerId->boolean(80), // 80% kemungkinan artikel berstatus published (true)
                'author_id' => 1, // Pastikan ada User dengan ID 1 di tabel users sebelum menjalankan seeder ini
                'category' => $fakerId->randomElement(['news', 'announcement', 'event', 'education']),

                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Insert data sekaligus (bulk insert)
        DB::table('articles')->insert($articles);
    }
}