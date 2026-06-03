<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Admin YHIE',
                'email' => 'admin@yhie.org',
                'password' => bcrypt('password'),
            ]
        );

        $this->call(MediaSeeder::class);
        $this->call(PartnerSeeder::class);
        $this->call(ProgramSeeder::class);
        $this->call(ScheduleSeeder::class);
        $this->call(ArticleSeeder::class);
        $this->call(SettingSeeder::class);
    }
}
