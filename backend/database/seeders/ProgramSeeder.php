<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Program;
use Faker\Factory as Faker;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Inisialisasi Faker untuk bahasa Indonesia dan Inggris
        $fakerId = Faker::create('id_ID');
        $fakerEn = Faker::create('en_US');

        // Topik untuk divariasikan ke dalam judul program
        $techTopics = [
            'Laravel Filament',
            'React JS',
            'FastAPI',
            'Python',
            'PHP',
            'Artificial Intelligence',
            'Large Language Models',
            'Optical Character Recognition',
            'Figma Design',
            'Enterprise Resource Planning'
        ];

        for ($i = 1; $i <= 50; $i++) {
            // Mengambil topik acak dari array di atas
            $topic = $techTopics[array_rand($techTopics)];

            // Membuat judul acak dengan tambahan topik
            $titleId = "Program Pelatihan " . $topic . " " . $fakerId->words(2, true);
            $titleEn = $topic . " " . $fakerEn->words(2, true) . " Training Program";

            Program::create([
                'title_id' => ucwords($titleId),
                'title_en' => ucwords($titleEn),
                'description_id' => $fakerId->paragraph(2), // Generate 2 paragraf bahasa Indonesia
                'description_en' => $fakerEn->paragraph(2), // Generate 2 paragraf bahasa Inggris
                'image_path' => 'images/programs/program-' . $i . '.jpg',
                'price_id' => $fakerId->randomFloat(2, 500000, 7500000), // Rp 500.000 - Rp 7.500.000
                'price_en' => $fakerEn->randomFloat(2, 50, 500),         // $50 - $500
                'slug_id' => Str::slug($titleId),
                'slug_en' => Str::slug($titleEn),
            ]);
        }
    }
}