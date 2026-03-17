<?php

namespace Database\Seeders;

use App\Models\GradeLevel;
use App\Models\Subject;
use App\Models\SubjectGradeOffering;
use Illuminate\Database\Seeder;

class SubjectsSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            ['code' => 'ru', 'name' => 'Русский язык'],
            ['code' => 'math', 'name' => 'Математика'],
            ['code' => 'bio', 'name' => 'Биология'],
            ['code' => 'hist', 'name' => 'История'],
            ['code' => 'geo', 'name' => 'География'],
            ['code' => 'soc', 'name' => 'Обществознание'],
            ['code' => 'phys', 'name' => 'Физика'],
            ['code' => 'chem', 'name' => 'Химия'],
        ];

        foreach ($subjects as $index => $subject) {
            $model = Subject::query()->updateOrCreate(
                ['code' => $subject['code']],
                [
                    'name' => $subject['name'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ],
            );

            foreach (GradeLevel::query()->pluck('id') as $gradeLevelId) {
                SubjectGradeOffering::query()->updateOrCreate(
                    [
                        'subject_id' => $model->id,
                        'grade_level_id' => $gradeLevelId,
                    ],
                    ['is_active' => true],
                );
            }
        }
    }
}
