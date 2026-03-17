<?php

namespace App\Services\Import;

use App\Models\Assessment;
use App\Models\GradeLevel;
use App\Models\QuestionType;
use App\Models\Subject;
use Illuminate\Support\Str;

class AssessmentImportMapper
{
    public function map(array $payload): array
    {
        $title = data_get($payload, 'assessment.title', 'assessment');
        $baseSlug = Str::slug($title) ?: 'assessment';
        $slug = $this->uniqueSlug($baseSlug);

        return [
            'source' => [
                'source_type' => data_get($payload, 'source.type', 'import'),
                'title' => data_get($payload, 'source.title', $title),
                'source_url' => data_get($payload, 'source.source_url'),
                'original_filename' => data_get($payload, 'source.original_file'),
                'answer_source_filename' => data_get($payload, 'source.answer_source_file'),
                'meta_json' => data_get($payload, 'source', []),
            ],
            'assessment' => [
                'title' => $title,
                'slug' => $slug,
                'subject_id' => Subject::query()->where('code', data_get($payload, 'assessment.subject_code'))->value('id'),
                'grade_level_id' => GradeLevel::query()->where('code', data_get($payload, 'assessment.grade_code'))->value('id'),
                'assessment_kind' => data_get($payload, 'assessment.assessment_kind', 'trainer'),
                'year_label' => data_get($payload, 'assessment.year_label'),
                'duration_minutes' => data_get($payload, 'assessment.duration_minutes'),
                'description' => data_get($payload, 'assessment.description'),
                'status' => 'draft',
                'meta_json' => data_get($payload, 'assessment', []),
            ],
            'grading_scale' => data_get($payload, 'grading_scale'),
            'sections' => collect($payload['sections'] ?? [])->map(function (array $section) {
                return [
                    'title' => $section['title'],
                    'position' => $section['position'] ?? 1,
                    'instruction_html' => $section['instruction_html'] ?? null,
                    'questions' => collect($section['questions'] ?? [])->map(function (array $question) {
                        return [
                            'external_number' => $question['external_number'],
                            'question_type_id' => QuestionType::query()
                                ->where('code', $question['question_type_code'])
                                ->value('id'),
                            'checking_mode' => $question['checking_mode'],
                            'prompt_html' => $question['prompt_html'],
                            'instruction_html' => $question['instruction_html'] ?? null,
                            'max_score' => $question['max_score'],
                            'requires_manual_review' => $question['requires_manual_review']
                                ?? in_array($question['checking_mode'], ['manual_open', 'manual_rubric'], true),
                            'position' => (int) preg_replace('/\D+/', '', (string) $question['external_number']) ?: 1,
                            'response_structure_json' => $question['response_structure'] ?? null,
                            'meta_json' => $question,
                            'options' => $question['options'] ?? [],
                            'answers' => $question['answers'] ?? [],
                            'rubric' => $question['rubric'] ?? null,
                        ];
                    })->all(),
                ];
            })->all(),
        ];
    }

    private function uniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug;
        $suffix = 2;

        while (Assessment::query()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
