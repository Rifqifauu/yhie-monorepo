<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kosongkan tabel sebelum di-seed
        DB::table('settings')->truncate();

        $now = Carbon::now();

        $settings = [
            [
                'id' => 1,
                'key' => 'site_name',
                'value' => 'Yayasan Harmoni Indonesia Emas',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'key' => 'site_description',
                'value' => 'Website Resmi Yayasan Harmoni Indonesia Emas (YHIE).',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'key' => 'contact_email',
                'value' => 'info@yhie.or.id',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'key' => 'contact_phone',
                'value' => '+62 812-3456-7890',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'key' => 'wa_number',
                'value' => '6281234567890',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'key' => 'about_history_id',
                'value' => 'Yayasan Hafiz Indonesia Emas (YHIE) didirikan dengan komitmen mendalam untuk mencetak generasi penghafal Al-Qur\'an unggul yang tidak hanya cakap secara spiritual, tetapi juga kompetitif secara akademis di kancah internasional. Berawal dari gerakan kecil dakwah Al-Qur\'an, kini YHIE berkembang pesat menjadi lembaga terakreditasi internasional yang dipercaya oleh ratusan mitra.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 7,
                'key' => 'about_history_en',
                'value' => 'Yayasan Hafiz Indonesia Emas (YHIE) was established with a profound commitment to nurturing exceptional Quranic memorizers who are spiritually sound and academically competitive on the international stage. Originating from a small Quranic outreach, YHIE has grown rapidly into an internationally accredited institution trusted by hundreds of partners.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 8,
                'key' => 'about_vision_id',
                'value' => 'Menjadi pusat unggulan pendidikan Al-Qur\'an bertaraf dunia yang melahirkan pemimpin berkarakter mulia, cerdas intelektual, dan berwawasan global.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 9,
                'key' => 'about_vision_en',
                'value' => 'To become a world-class center of Quranic educational excellence, nurturing noble-characterized, intellectually bright, and globally minded leaders.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 10,
                'key' => 'about_mission_id',
                'value' => "• Mengembangkan kurikulum tahfidz inovatif bebas stres berstandar internasional.\n• Mengintegrasikan pendidikan Al-Qur'an dengan wisata tadabbur alam dan sains.\n• Membangun kemitraan strategis global demi perluasan manfaat dakwah Al-Qur'an.",
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 11,
                'key' => 'about_mission_en',
                'value' => "• Developing stress-free, innovative, and internationally standardized tahfidz curriculums.\n• Integrating Quranic education with nature retreats and sciences.\n• Building strategic global partnerships to expand the benefits of Quranic dakwah.",
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 12,
                'key' => 'bank_name',
                'value' => 'Bank Syariah Indonesia (BSI)',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 13,
                'key' => 'bank_account_number',
                'value' => '7123456789',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 14,
                'key' => 'bank_account_name',
                'value' => 'Yayasan Hafiz Indonesia Emas',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 15,
                'key' => 'office_address',
                'value' => 'Baki Desa TEJASARI, Kaligondang, Purbalingga, Jawa Tengah, Indonesia',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 16,
                'key' => 'social_facebook',
                'value' => 'https://facebook.com',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 17,
                'key' => 'social_instagram',
                'value' => 'https://instagram.com',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 18,
                'key' => 'social_youtube',
                'value' => 'https://youtube.com',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 19,
                'key' => 'social_twitter',
                'value' => 'https://x.com',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 20,
                'key' => 'social_tiktok',
                'value' => 'https://tiktok.com',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('settings')->insert($settings);
    }
}
