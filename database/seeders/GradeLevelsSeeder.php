<?php

namespace Database\Seeders;

use App\Models\GradeLevel;
use Illuminate\Database\Seeder;

class GradeLevelsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (range(5, 11) as $grade) {
            GradeLevel::query()->updateOrCreate(
                ['code' => (string) $grade],
                [
                    'name' => "{$grade} класс",
                    'sort_order' => $grade,
                    'is_active' => true,
                ],
            );
        }
    }
}
