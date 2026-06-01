<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $partners = [];

        // Looping untuk membuat 50 data
        for ($i = 1; $i <= 50; $i++) {
            $partners[] = [
                'name_id'        => 'Mitra ' . $i,
                'description_id' => 'Ini adalah deskripsi untuk Mitra ' . $i . ' dalam bahasa Indonesia.',
                'name_en'        => 'Partner ' . $i,
                'description_en' => 'This is the description for Partner ' . $i . ' in English.',
                'slug_id'        => 'mitra-' . $i,
                'slug_en'        => 'partner-' . $i,
                'logo'           => 'path/to/logo-partner-' . $i . '.png',
                // Pastikan menambahkan created_at dan updated_at jika di migration ada $table->timestamps()
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }

        // Insert data ke tabel 'partners' dalam bentuk bulk/batch
        DB::table('partners')->insert($partners);
    }
}