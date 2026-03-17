<?php

namespace App\Services\Import;

class AssessmentPreviewBuilder
{
    public function build(array $payload): array
    {
        $sections = collect($payload['sections'] ?? []);
        $questions = $sections->flatMap(fn (array $section) => $section['questions'] ?? []);

        return [
            'title' => data_get($payload, 'assessment.title'),
            'subject_code' => data_get($payload, 'assessment.subject_code'),
            'grade_code' => data_get($payload, 'assessment.grade_code'),
            'section_count' => $sections->count(),
            'question_count' => $questions->count(),
            'manual_question_count' => $questions
                ->where('requires_manual_review', true)
                ->count(),
            'rubric_question_count' => $questions
                ->filter(fn (array $question) => filled($question['rubric'] ?? null))
                ->count(),
            'max_score' => $questions->sum(fn (array $question) => (float) ($question['max_score'] ?? 0)),
            'has_grading_scale' => filled($payload['grading_scale'] ?? null),
        ];
    }
}
