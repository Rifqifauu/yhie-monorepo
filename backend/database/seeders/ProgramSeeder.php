<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Program;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = [
            [
                "title_id" => "Program 1",
                "description_id" => "Deskripsi untuk Program 1",
                "title_en" => "Program 1",
                "description_en" => "Description for Program 1",
                "start_date" => "2026-05-27 00:00:00",
                "end_date" => "2026-05-28 00:00:00",
            ],
            [
                "title_id" => "Program 2",
                "description_id" => "Deskripsi untuk Program 2",
                "title_en" => "Program 2",
                "description_en" => "Description for Program 2",
                "start_date" => "2026-05-28 00:00:00",
                "end_date" => "2026-05-29 00:00:00",
            ],
        ];

        Program::insert($programs);
    }
}
