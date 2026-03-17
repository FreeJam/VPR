<?php

namespace Database\Seeders;

use App\Models\QuestionType;
use Illuminate\Database\Seeder;

class QuestionTypesSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['code' => 'single_choice', 'name' => 'Single choice', 'default_checking_mode' => 'auto', 'is_objective' => true],
            ['code' => 'multiple_choice', 'name' => 'Multiple choice', 'default_checking_mode' => 'auto', 'is_objective' => true],
            ['code' => 'short_text', 'name' => 'Short text', 'default_checking_mode' => 'auto', 'is_objective' => true],
            ['code' => 'numeric', 'name' => 'Numeric', 'default_checking_mode' => 'auto', 'is_objective' => true],
            ['code' => 'open_response', 'name' => 'Open response', 'default_checking_mode' => 'manual_open', 'is_objective' => false],
            ['code' => 'compound_open_response', 'name' => 'Compound open response', 'default_checking_mode' => 'manual_rubric', 'is_objective' => false],
            ['code' => 'multi_field_text', 'name' => 'Multi field text', 'default_checking_mode' => 'hybrid', 'is_objective' => true],
            ['code' => 'language_analysis', 'name' => 'Language analysis', 'default_checking_mode' => 'manual_rubric', 'is_objective' => false],
            ['code' => 'matching', 'name' => 'Matching', 'default_checking_mode' => 'auto', 'is_objective' => true],
            ['code' => 'cloze_text', 'name' => 'Cloze text', 'default_checking_mode' => 'hybrid', 'is_objective' => true],
            ['code' => 'essay', 'name' => 'Essay', 'default_checking_mode' => 'manual_rubric', 'is_objective' => false],
        ];

        foreach ($types as $type) {
            QuestionType::query()->updateOrCreate(
                ['code' => $type['code']],
                $type,
            );
        }
    }
}
