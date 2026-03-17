<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            GradeLevelsSeeder::class,
            SubjectsSeeder::class,
            QuestionTypesSeeder::class,
            DemoUsersSeeder::class,
        ]);

        AcademicYear::query()->updateOrCreate(
            ['name' => '2025/2026'],
            [
                'starts_on' => '2025-09-01',
                'ends_on' => '2026-05-31',
                'is_active' => true,
            ],
        );
    }
}
