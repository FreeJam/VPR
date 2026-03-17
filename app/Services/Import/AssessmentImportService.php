<?php

namespace App\Services\Import;

use App\Models\Assessment;
use App\Models\AssessmentImportLink;
use App\Models\AssessmentSection;
use App\Models\AssessmentVersion;
use App\Models\ContentSource;
use App\Models\GradingScale;
use App\Models\ImportBatch;
use App\Models\ImportError;
use App\Models\Question;
use App\Models\QuestionAnswer;
use App\Models\QuestionOption;
use App\Models\Rubric;
use App\Models\RubricCriterion;
use App\Models\RubricLevel;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class AssessmentImportService
{
    public function __construct(
        private readonly AssessmentImportValidator $validator,
        private readonly AssessmentPreviewBuilder $previewBuilder,
        private readonly AssessmentImportMapper $mapper,
    ) {
    }

    public function upload(UploadedFile $file, User $user): ImportBatch
    {
        $contents = $file->get();
        $payloadHash = sha1($contents);

        $existing = ImportBatch::query()
            ->where('payload_hash', $payloadHash)
            ->first();

        if ($existing) {
            return $existing->load(['errors', 'assessmentLink']);
        }

        $storedPath = 'imports/'.now()->format('YmdHis').'-'.Str::random(8).'.json';
        Storage::disk('local')->put($storedPath, $contents);

        $validation = $this->validator->validateUploadedFile($file);
        $preview = $validation['payload']
            ? $this->previewBuilder->build($validation['payload'])
            : null;

        $batch = ImportBatch::query()->create([
            'user_id' => $user->id,
            'source_type' => 'json',
            'original_filename' => $file->getClientOriginalName(),
            'stored_path' => $storedPath,
            'format_version' => data_get($validation, 'payload.format_version', '1.0'),
            'status' => $validation['is_valid'] ? 'validated' : 'failed',
            'payload_hash' => $payloadHash,
            'total_items' => $preview['question_count'] ?? 0,
            'imported_items' => 0,
            'error_count' => count($validation['errors']),
            'preview_json' => $preview,
        ]);

        $this->syncErrors($batch, $validation['errors']);

        return $batch->load(['errors', 'assessmentLink']);
    }

    public function preview(ImportBatch $batch): array
    {
        if ($batch->preview_json) {
            return $batch->preview_json;
        }

        $payload = $this->loadPayload($batch);
        $preview = $this->previewBuilder->build($payload);

        $batch->update(['preview_json' => $preview]);

        return $preview;
    }

    public function run(ImportBatch $batch): Assessment
    {
        if ($batch->status === 'imported' && $batch->assessmentLink) {
            return $batch->assessmentLink->assessment()->firstOrFail();
        }

        $payload = $this->loadPayload($batch);
        $validation = $this->validator->validateDecodedPayload($payload);

        if (! $validation['is_valid']) {
            $batch->update([
                'status' => 'failed',
                'error_count' => count($validation['errors']),
            ]);

            $this->syncErrors($batch, $validation['errors']);

            throw new RuntimeException('Import payload is invalid and cannot be imported.');
        }

        $mapped = $this->mapper->map($payload);

        return DB::transaction(function () use ($batch, $mapped): Assessment {
            $source = ContentSource::query()->create([
                ...$mapped['source'],
                'payload_hash' => $batch->payload_hash,
            ]);

            $assessment = Assessment::query()->create([
                ...$mapped['assessment'],
                'content_source_id' => $source->id,
                'created_by' => $batch->user_id,
            ]);

            $gradingScale = null;

            if ($mapped['grading_scale']) {
                $gradingScale = GradingScale::query()->create([
                    'assessment_id' => $assessment->id,
                    'title' => data_get($mapped, 'grading_scale.title', $assessment->title.' scale'),
                    'max_primary_score' => data_get($mapped, 'grading_scale.max_primary_score', 0),
                    'meta_json' => $mapped['grading_scale'],
                ]);

                foreach (data_get($mapped, 'grading_scale.ranges', []) as $index => $range) {
                    $gradingScale->ranges()->create([
                        'grade_label' => $range['grade_label'],
                        'min_score' => $range['min_score'],
                        'max_score' => $range['max_score'],
                        'position' => $index + 1,
                    ]);
                }
            }

            $version = AssessmentVersion::query()->create([
                'assessment_id' => $assessment->id,
                'grading_scale_id' => $gradingScale?->id,
                'version_label' => 'v1',
                'status' => 'draft',
                'is_current' => true,
                'imported_at' => now(),
                'meta_json' => ['import_batch_id' => $batch->id],
            ]);

            foreach ($mapped['sections'] as $sectionData) {
                $section = AssessmentSection::query()->create([
                    'assessment_version_id' => $version->id,
                    'title' => $sectionData['title'],
                    'instruction_html' => $sectionData['instruction_html'],
                    'position' => $sectionData['position'],
                    'meta_json' => null,
                ]);

                foreach ($sectionData['questions'] as $questionData) {
                    $question = Question::query()->create([
                        'assessment_section_id' => $section->id,
                        'question_type_id' => $questionData['question_type_id'],
                        'external_number' => $questionData['external_number'],
                        'checking_mode' => $questionData['checking_mode'],
                        'prompt_html' => $questionData['prompt_html'],
                        'instruction_html' => $questionData['instruction_html'],
                        'max_score' => $questionData['max_score'],
                        'requires_manual_review' => $questionData['requires_manual_review'],
                        'position' => $questionData['position'],
                        'response_structure_json' => $questionData['response_structure_json'],
                        'meta_json' => $questionData['meta_json'],
                    ]);

                    foreach ($questionData['options'] as $optionIndex => $option) {
                        QuestionOption::query()->create([
                            'question_id' => $question->id,
                            'option_key' => $option['key'],
                            'text' => $option['text'],
                            'is_correct' => (bool) ($option['is_correct'] ?? false),
                            'position' => $optionIndex + 1,
                            'meta_json' => $option,
                        ]);
                    }

                    foreach ($questionData['answers'] as $answer) {
                        QuestionAnswer::query()->create([
                            'question_id' => $question->id,
                            'answer_kind' => $answer['answer_kind'],
                            'answer_value' => $answer['answer_value'],
                            'meta_json' => $answer,
                        ]);
                    }

                    if ($questionData['rubric']) {
                        $rubric = Rubric::query()->create([
                            'question_id' => $question->id,
                            'title' => $questionData['rubric']['title'] ?? 'Rubric',
                            'scoring_mode' => $questionData['rubric']['scoring_mode'] ?? 'sum',
                            'description' => $questionData['rubric']['description'] ?? null,
                        ]);

                        foreach (($questionData['rubric']['criteria'] ?? []) as $criterionIndex => $criterion) {
                            $criterionModel = RubricCriterion::query()->create([
                                'rubric_id' => $rubric->id,
                                'code' => $criterion['code'],
                                'title' => $criterion['title'],
                                'description' => $criterion['description'] ?? null,
                                'max_points' => $criterion['max_points'] ?? 0,
                                'position' => $criterionIndex + 1,
                            ]);

                            foreach (($criterion['levels'] ?? []) as $levelIndex => $level) {
                                RubricLevel::query()->create([
                                    'rubric_criterion_id' => $criterionModel->id,
                                    'points' => $level['points'],
                                    'description' => $level['description'] ?? null,
                                    'position' => $levelIndex + 1,
                                ]);
                            }
                        }
                    }
                }
            }

            AssessmentImportLink::query()->create([
                'import_batch_id' => $batch->id,
                'assessment_id' => $assessment->id,
                'assessment_version_id' => $version->id,
            ]);

            $batch->errors()->delete();
            $batch->update([
                'status' => 'imported',
                'imported_items' => $batch->total_items,
                'error_count' => 0,
            ]);

            return $assessment->fresh(['versions.sections.questions']);
        });
    }

    private function loadPayload(ImportBatch $batch): array
    {
        $contents = Storage::disk('local')->get($batch->stored_path);
        $payload = json_decode($contents, true);

        if (! is_array($payload)) {
            throw new RuntimeException('Stored import file is unreadable.');
        }

        return $payload;
    }

    private function syncErrors(ImportBatch $batch, array $errors): void
    {
        $batch->errors()->delete();

        foreach ($errors as $error) {
            ImportError::query()->create([
                'import_batch_id' => $batch->id,
                'entity_type' => $error['entity_type'] ?? null,
                'entity_index' => $error['entity_index'] ?? null,
                'field_name' => $error['field_name'] ?? null,
                'error_message' => $error['error_message'],
                'raw_payload_json' => $error['raw_payload_json'] ?? null,
            ]);
        }
    }
}
