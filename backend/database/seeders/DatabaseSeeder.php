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
        // User::factory(10)->create();

<<<<<<< Updated upstream
        User::factory()->create([
            "name" => "Test User",
            "email" => "test@example.com",
            "password" => bcrypt("password"),
        ]);
=======
        $this->call(MediaSeeder::class);
        $this->call(PartnerSeeder::class);
        $this->call(ProgramSeeder::class);
>>>>>>> Stashed changes
    }
}
