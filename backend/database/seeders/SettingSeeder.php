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
        ];

        DB::table('settings')->insert($settings);
    }
}
