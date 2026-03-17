<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Attempt;
use App\Models\AttemptQuestionAnswer;
use App\Models\AttemptQuestionReview;
use App\Models\Question;
use App\Models\StudentProfile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AttemptFlowService
{
    public function __construct(
        private readonly AnswerCheckingService $answerCheckingService,
        private readonly GradeCalculationService $gradeCalculationService,
    ) {
    }

    public function startOrResume(Assignment $assignment, StudentProfile $studentProfile): Attempt
    {
        if ($assignment->student_profile_id !== $studentProfile->id) {
            throw new RuntimeException('Assignment is not available for this student.');
        }

        if (! $assignment->is_published || $assignment->status !== 'published') {
            throw new RuntimeException('Assignment is not published yet.');
        }

        $existing = Attempt::query()
            ->where('assignment_id', $assignment->id)
            ->where('student_profile_id', $studentProfile->id)
            ->where('status', 'in_progress')
            ->latest('attempt_number')
            ->first();

        if ($existing) {
            return $existing;
        }

        $attemptCount = Attempt::query()
            ->where('assignment_id', $assignment->id)
            ->where('student_profile_id', $studentProfile->id)
            ->count();

        if ($attemptCount >= $assignment->max_attempts) {
            throw new RuntimeException('Maximum number of attempts has already been used.');
        }

        return Attempt::query()->create([
            'assignment_id' => $assignment->id,
            'student_profile_id' => $studentProfile->id,
            'status' => 'in_progress',
            'attempt_number' => $attemptCount + 1,
            'started_at' => now(),
        ]);
    }

    public function saveDraft(Attempt $attempt, array $answers): Attempt
    {
        return DB::transaction(function () use ($attempt, $answers) {
            $this->syncResponses($attempt, $answers, false);

            $attempt = $this->gradeCalculationService->recalculateAttempt($attempt->fresh());
            $attempt->update(['status' => 'in_progress']);

            return $attempt->fresh();
        });
    }

    public function submit(Attempt $attempt, array $answers): Attempt
    {
        return DB::transaction(function () use ($attempt, $answers) {
            $this->syncResponses($attempt, $answers, true);

            $pendingManualReview = false;

            foreach ($this->questions($attempt) as $question) {
                if (! $question->requires_manual_review) {
                    continue;
                }

                AttemptQuestionReview::query()->firstOrCreate(
                    [
                        'attempt_id' => $attempt->id,
                        'question_id' => $question->id,
                    ],
                    [
                        'reviewer_id' => null,
                        'comment' => null,
                        'awarded_score' => 0,
                        'reviewed_at' => null,
                    ],
                );

                $pendingManualReview = true;
            }

            $attempt = $this->gradeCalculationService->recalculateAttempt($attempt->fresh());
            $attempt->update([
                'status' => $pendingManualReview ? 'pending_review' : 'checked',
                'submitted_at' => now(),
                'checked_at' => $pendingManualReview ? null : now(),
            ]);

            return $attempt->fresh();
        });
    }

    private function syncResponses(Attempt $attempt, array $answers, bool $createBlankRecords): void
    {
        foreach ($this->questions($attempt) as $question) {
            $response = $answers[$question->id] ?? null;

            if ($this->isBlankResponse($response)) {
                if ($createBlankRecords) {
                    $this->persistBlankAnswer($attempt, $question);
                } else {
                    AttemptQuestionAnswer::query()
                        ->where('attempt_id', $attempt->id)
                        ->where('question_id', $question->id)
                        ->delete();
                }

                continue;
            }

            $normalized = $this->normalizeResponse($question, $response);
            $autoScore = $this->shouldAutoScore($question)
                ? $this->answerCheckingService->scoreQuestion($question, $normalized['scoring'])
                : 0.0;

            AttemptQuestionAnswer::query()->updateOrCreate(
                [
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                ],
                [
                    'response_json' => $normalized['json'],
                    'response_text' => $normalized['text'],
                    'auto_score' => $autoScore,
                    'manual_score' => 0,
                    'is_finalized' => ! $question->requires_manual_review,
                ],
            );
        }
    }

    private function persistBlankAnswer(Attempt $attempt, Question $question): void
    {
        AttemptQuestionAnswer::query()->updateOrCreate(
            [
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
            ],
            [
                'response_json' => null,
                'response_text' => null,
                'auto_score' => 0,
                'manual_score' => 0,
                'is_finalized' => ! $question->requires_manual_review,
            ],
        );
    }

    private function questions(Attempt $attempt): Collection
    {
        $attempt->loadMissing([
            'assignment.assessmentVersion.sections.questions.questionType',
            'assignment.assessmentVersion.sections.questions.options',
            'assignment.assessmentVersion.sections.questions.answers',
            'assignment.assessmentVersion.sections.questions.rubric.criteria.levels',
        ]);

        return $attempt->assignment->assessmentVersion->sections
            ->sortBy('position')
            ->flatMap(fn ($section) => $section->questions->sortBy('position'))
            ->values();
    }

    private function shouldAutoScore(Question $question): bool
    {
        return in_array($question->checking_mode, ['auto', 'hybrid'], true);
    }

    private function normalizeResponse(Question $question, mixed $response): array
    {
        $typeCode = $question->questionType->code;
        $structure = $question->response_structure_json['parts'] ?? null;

        if ($typeCode === 'multiple_choice') {
            $values = collect((array) $response)
                ->map(fn ($value) => trim((string) $value))
                ->filter()
                ->values()
                ->all();

            return [
                'json' => $values,
                'text' => implode(', ', $values),
                'scoring' => $values,
            ];
        }

        if ($typeCode === 'multi_field_text') {
            $values = is_array($response)
                ? collect($response)->map(fn ($value) => trim((string) $value))->filter()->values()->all()
                : collect(preg_split('/[\r\n,;]+/u', (string) $response) ?: [])
                    ->map(fn ($value) => trim((string) $value))
                    ->filter()
                    ->values()
                    ->all();

            return [
                'json' => $values,
                'text' => implode(PHP_EOL, $values),
                'scoring' => $values,
            ];
        }

        if ($structure && is_array($response)) {
            $parts = collect($structure)
                ->mapWithKeys(function (array $part) use ($response) {
                    $value = trim((string) ($response[$part['code']] ?? ''));

                    return [$part['code'] => $value];
                })
                ->all();

            return [
                'json' => $parts,
                'text' => collect($parts)
                    ->filter(fn ($value) => $value !== '')
                    ->map(fn ($value, $key) => "{$key}: {$value}")
                    ->implode(PHP_EOL),
                'scoring' => array_values(array_filter($parts, fn ($value) => $value !== '')),
            ];
        }

        $value = is_array($response)
            ? collect($response)->map(fn ($item) => trim((string) $item))->filter()->implode(PHP_EOL)
            : trim((string) $response);

        return [
            'json' => $value,
            'text' => $value,
            'scoring' => $value,
        ];
    }

    private function isBlankResponse(mixed $response): bool
    {
        if ($response === null) {
            return true;
        }

        if (is_string($response)) {
            return trim($response) === '';
        }

        if (is_array($response)) {
            return collect($response)->every(fn ($value) => $this->isBlankResponse($value));
        }

        return false;
    }
}
