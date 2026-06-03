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
        $templates = [
            [
                'category' => 'academic',
                'title_id' => 'Sanctuary of Knowledge',
                'title_en' => 'Sanctuary of Knowledge',
                'description_id' => 'Perspektif megah dari detail arsitektur Islam dengan lengkungan tinggi dan pola geometris.',
                'description_en' => 'A grand perspective of intricate Islamic architectural details with high arches and geometric patterns.',
                'image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCYux_hJAayRbruvqHl9-aqkzNFmccYwDmzUXGVUpuyzZlmyetxLDHPllpgD0e3mmmjzQOf1igIMUsZyiS5gNfTPYCyyRbzCFHRenRHlZYnxfuj2pbwB2Q_2cfywr81xYqoSrtZ9YZUOHQWGy_H7xzHxx1QPdQ_X02KmFjoZHI2wNW1atlyQhgCwFyQId2J-lxWp3h9eIQkEDONw9eeddFYNgPuH-DQIm4ew9Lwlh1G0TBKes8AVbxK4jGNmlHy1sboZV-EL7XgYU8',
            ],
            [
                'category' => 'graduation',
                'title_id' => 'Cohort of 2024',
                'title_en' => 'Cohort of 2024',
                'description_id' => 'Mahasiswa mengenakan jubah wisuda hijau tua yang elegan dan tersenyum gembira di luar ruangan.',
                'description_en' => 'A group of diverse students in elegant dark green graduation gowns and caps, smiling joyfully in a sunlit outdoor campus.',
                'image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBGlsnV8sEkDOjkt4Mj0jMWi7BX3_Ke9qQPJqYzDEcDVwBJmsoNrueSrTAcfOxhptEQ05EOS5dD58_VrC0bnsuNZf3bNNY_370qj4_D3hkAE_55I4rRNrkwuvD6v0GbEZTb8D_fb6XTAAgtUCz0eEt00iQe64yQ0e3L5C_Dj1bEyQJaV7fHP_jSDIRrvTAzkP3U0nq6PWrCwEM8lWkQ4p0nfiPECAQ_5mI0yQ1o1pevntsBO1y3vz3cj0xLVJfchyWzcKRSiEktuyw',
            ],
            [
                'category' => 'academic',
                'title_id' => 'Spiritual Foundations',
                'title_en' => 'Spiritual Foundations',
                'description_id' => 'Detail Al-Qur\'an terbuka di atas dudukan kayu di perpustakaan modern dengan pencahayaan lembut.',
                'description_en' => 'Close up of an open Quran on a wooden stand in a softly lit, modern library with ivory walls.',
                'image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCi_OUxIGJ9a66KE0Ocbipb2CfddvuVPt3_HBOwnyRZIBsamyC4Vz_-AW92aRldjDbZvFn9DMq0YdeTFDmJI7yuH8YrDpQ1K-9czkuMz8w49ksR42HEofiznqlIFhoCQOrhyjgxi2G8162OvX0W2t8SNrW9-TN8WYKYT1k70KkI9Xpx38cV1ojH89UTedUTYBuckBvkE-b1H3lKBiZ-jlgZfvCmSe5BGx6Bfuag80NAA4WijjHS3FlWVGZNWKlrdwmiANj7N97ZZuM',
            ],
            [
                'category' => 'tourism',
                'title_id' => 'Serenity of Tejasari',
                'title_en' => 'Serenity of Tejasari',
                'description_id' => 'Pemandangan perbukitan hijau Tejasari di pagi hari yang berkabut dengan sinar matahari emas.',
                'description_en' => 'A breathtaking view of lush, terraced green hills in Tejasari during the early morning golden hour.',
                'image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAybOmxtGMl-peQQGzLrgOzMdg2kncK7Nbie9zJa5vu-EUhPnFHJi066VMeWtwP6zN2IUJ1e02dVwgT2BjAI-cwZIHXiaoRQIhlnInjyGhT3A7OMRs23xdWSswjR9dmaa_slLeyAlEapZfOQuC_vdu8OvR462VwAayMJl8CQuDRJ_yzpauKc00DdomoHej0RueOIR_-5uGDDi6v01aY95b244xTFAUdZCOzvlVBG0touNRyOb8ZgaqwF72ttM_KyH0je8aX4O7iGGw',
            ],
            [
                'category' => 'social',
                'title_id' => 'Communal Bonds',
                'title_en' => 'Communal Bonds',
                'description_id' => 'Pertemuan komunitas di aula modern yang bersih dan terang, mendiskusikan program sosial.',
                'description_en' => 'A warm and inviting community gathering of diverse individuals in a clean, brightly lit modern hall with high ceilings.',
                'image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCp7hxSNRmofgugCzWAWfuPRE8v-zhXh6pC9CFmwXB0EzUE2IAOFkr3SvgCEUPv9u6zVII3JI-Tc-Dr3L8WU21HZxciz7AcrEc4z0CQVjrGLTZXtTiajHJYfGsfqye_oKQFCfrNO1-PERdcX3NuCXRVRP0lpqIXWDFgqrpHjE-UFq_E6todvA5cidtEx5tHifST5WBNwzUCfboTW8jYeERvgnPNYSI4m8GqWloFnQX734QGLzSp0wH5V1tQmHy5smuOFaz_dxThenk',
            ],
            [
                'category' => 'academic',
                'title_id' => 'The Pursuit of Excellence',
                'title_en' => 'The Pursuit of Excellence',
                'description_id' => 'Meja belajar minimalis dengan buku catatan dan laptop, disinari cahaya alami dari jendela.',
                'description_en' => 'A minimalist and focused academic setting showing a person\'s hands writing notes in a clean, ivory notebook next to a modern laptop.',
                'image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCCBRmKN37ekJzXTmklG7ttG8uHkojM97yJhbipOtwk4k9klffYQkeFD2vfvpxXnggE7YAyZ6ibGuMWrZGQGLi9exYjO4HJCtAzQXPMBSNL2DNS0q8-mWpO3clBTBKrllhs0MpDXzPuZQNbce2t2mKcfy3JvbiE028NNg-DRbPE3lFycF3ChVg6EU3oo4DNlP-nwyGEeZIxFbfDfHYjqN4dLqjte-X2BBAx7hGa_mIbTPk220yaBqibLzzMAJ3eZDvmoUMsP0m8OLQ',
            ]
        ];

        $mediaItems = [];
        for ($i = 1; $i <= 50; $i++) {
            $tpl = $templates[($i - 1) % count($templates)];
            $mediaItems[] = [
                'title_id' => $tpl['title_id'] . " ({$i})",
                'title_en' => $tpl['title_en'] . " ({$i})",
                'description_id' => $tpl['description_id'],
                'description_en' => $tpl['description_en'],
                'slug_id' => "media-" . \Str::slug($tpl['title_id']) . "-{$i}",
                'slug_en' => "media-" . \Str::slug($tpl['title_en']) . "-{$i}",
                'category' => $tpl['category'],
                'image' => json_encode([
                    'path' => $tpl['image_url'],
                ]),
                'created_at' => now()->subHours(50 - $i),
                'updated_at' => now()->subHours(50 - $i),
            ];
        }

        foreach ($mediaItems as $media) {
            \DB::table('media')->insert($media);
        }
    }
}
