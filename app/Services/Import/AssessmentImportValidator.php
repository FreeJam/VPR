<?php

namespace App\Services\Import;

use App\Models\GradeLevel;
use App\Models\QuestionType;
use App\Models\Subject;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class AssessmentImportValidator
{
    public function validateUploadedFile(UploadedFile $file): array
    {
        $validator = Validator::make(
            ['file' => $file],
            ['file' => ['required', 'file', 'max:10240']]
        );

        $validator->after(function ($validator) use ($file): void {
            if (! in_array(strtolower($file->getClientOriginalExtension()), ['json', 'txt'], true)) {
                $validator->errors()->add('file', 'Import file must be a JSON document.');
            }
        });

        if ($validator->fails()) {
            return [
                'is_valid' => false,
                'payload' => null,
                'warnings' => [],
                'errors' => $this->formatErrors($validator->errors()->messages()),
            ];
        }

        $decoded = json_decode($file->get(), true);

        if (! is_array($decoded)) {
            return [
                'is_valid' => false,
                'payload' => null,
                'warnings' => [],
                'errors' => [[
                    'field_name' => 'file',
                    'error_message' => 'Import file contains invalid JSON.',
                ]],
            ];
        }

        return $this->validateDecodedPayload($decoded);
    }

    public function validateDecodedPayload(array $payload): array
    {
        $validator = Validator::make($payload, [
            'format_version' => ['required', 'in:1.0'],
            'assessment.title' => ['required', 'string'],
            'assessment.subject_code' => ['required', 'string'],
            'assessment.grade_code' => ['required', 'string'],
            'assessment.assessment_kind' => ['required', 'string'],
            'sections' => ['required', 'array', 'min:1'],
            'sections.*.title' => ['required', 'string'],
            'sections.*.position' => ['required', 'integer', 'min:1'],
            'sections.*.questions' => ['required', 'array', 'min:1'],
            'sections.*.questions.*.external_number' => ['required', 'string'],
            'sections.*.questions.*.question_type_code' => ['required', 'string'],
            'sections.*.questions.*.checking_mode' => ['required', 'string'],
            'sections.*.questions.*.prompt_html' => ['required', 'string'],
            'sections.*.questions.*.max_score' => ['required', 'numeric', 'min:0'],
            'grading_scale.ranges' => ['nullable', 'array'],
            'grading_scale.ranges.*.grade_label' => ['required_with:grading_scale.ranges', 'string'],
            'grading_scale.ranges.*.min_score' => ['required_with:grading_scale.ranges', 'numeric'],
            'grading_scale.ranges.*.max_score' => ['required_with:grading_scale.ranges', 'numeric'],
        ]);

        $validator->after(function ($validator) use ($payload): void {
            if (! Subject::query()->where('code', data_get($payload, 'assessment.subject_code'))->exists()) {
                $validator->errors()->add('assessment.subject_code', 'Subject code does not exist.');
            }

            if (! GradeLevel::query()->where('code', data_get($payload, 'assessment.grade_code'))->exists()) {
                $validator->errors()->add('assessment.grade_code', 'Grade code does not exist.');
            }

            foreach (Arr::get($payload, 'sections', []) as $sectionIndex => $section) {
                foreach (Arr::get($section, 'questions', []) as $questionIndex => $question) {
                    $typeCode = $question['question_type_code'] ?? null;

                    if ($typeCode && ! QuestionType::query()->where('code', $typeCode)->exists()) {
                        $validator->errors()->add(
                            "sections.$sectionIndex.questions.$questionIndex.question_type_code",
                            'Question type code does not exist.'
                        );
                    }

                    if (($question['checking_mode'] ?? null) === 'auto'
                        && blank($question['answers'] ?? null)
                        && blank($question['options'] ?? null)) {
                        $validator->errors()->add(
                            "sections.$sectionIndex.questions.$questionIndex.checking_mode",
                            'Auto-checked questions must define answers or options.'
                        );
                    }

                    $criteria = Arr::get($question, 'rubric.criteria', []);

                    if ($criteria !== []) {
                        $codes = collect($criteria)->pluck('code')->filter();
                        if ($codes->count() !== $codes->unique()->count()) {
                            $validator->errors()->add(
                                "sections.$sectionIndex.questions.$questionIndex.rubric.criteria",
                                'Rubric criterion codes must be unique.'
                            );
                        }
                    }
                }
            }

            $ranges = collect(data_get($payload, 'grading_scale.ranges', []))
                ->sortBy('min_score')
                ->values();

            foreach ($ranges as $index => $range) {
                if (($range['min_score'] ?? 0) > ($range['max_score'] ?? 0)) {
                    $validator->errors()->add(
                        "grading_scale.ranges.$index",
                        'Grading scale range minimum cannot be greater than maximum.'
                    );
                }

                $previous = $ranges->get($index - 1);
                if ($previous && ($range['min_score'] ?? 0) <= ($previous['max_score'] ?? -1)) {
                    $validator->errors()->add(
                        "grading_scale.ranges.$index",
                        'Grading scale ranges must not overlap.'
                    );
                }
            }
        });

        $warnings = [];

        if (blank($payload['grading_scale'] ?? null)) {
            $warnings[] = 'Import payload has no grading scale.';
        }

        return [
            'is_valid' => ! $validator->fails(),
            'payload' => $payload,
            'warnings' => $warnings,
            'errors' => $this->formatErrors($validator->errors()->messages()),
        ];
    }

    private function formatErrors(array $messages): array
    {
        $errors = [];

        foreach ($messages as $field => $fieldMessages) {
            foreach ($fieldMessages as $message) {
                $errors[] = [
                    'field_name' => $field,
                    'error_message' => $message,
                ];
            }
        }

        return $errors;
    }
}
