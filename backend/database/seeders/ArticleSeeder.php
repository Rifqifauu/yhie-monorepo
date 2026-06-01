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

                // Karena kita menggunakan DB::table()->insert(), Eloquent casting tidak berjalan.
                // Jadi kita harus melakukan json_encode secara manual agar bisa masuk ke database.
                'image' => json_encode([
                    '/storage/images/articles/dummy-1.jpg',
                    '/storage/images/articles/dummy-2.jpg'
                ]),

                'is_published' => $fakerId->boolean(80), // 80% kemungkinan artikel berstatus published (true)
                'author_id' => 1, // Pastikan ada User dengan ID 1 di tabel users sebelum menjalankan seeder ini
                'category' => $fakerId->randomElement(['news', 'announcement', 'event']),

                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Insert data sekaligus (bulk insert)
        DB::table('articles')->insert($articles);
    }
}