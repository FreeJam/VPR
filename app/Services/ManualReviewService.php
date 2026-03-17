<?php

namespace App\Services;

use App\Models\AttemptCriterionScore;
use App\Models\AttemptQuestionAnswer;
use App\Models\AttemptQuestionReview;
use Illuminate\Support\Facades\DB;

class ManualReviewService
{
    public function __construct(
        private readonly GradeCalculationService $gradeCalculationService,
    ) {
    }

    public function applyReview(AttemptQuestionReview $review, array $criterionScores, ?string $comment = null): AttemptQuestionReview
    {
        return DB::transaction(function () use ($review, $criterionScores, $comment) {
            $review->criterionScores()->delete();

            $awardedScore = 0.0;

            foreach ($criterionScores as $criterionId => $points) {
                $score = AttemptCriterionScore::query()->create([
                    'attempt_question_review_id' => $review->id,
                    'rubric_criterion_id' => $criterionId,
                    'points' => $points,
                ]);

                $awardedScore += (float) $score->points;
            }

            $review->update([
                'comment' => $comment,
                'awarded_score' => $awardedScore,
                'reviewed_at' => now(),
            ]);

            AttemptQuestionAnswer::query()
                ->where('attempt_id', $review->attempt_id)
                ->where('question_id', $review->question_id)
                ->update([
                    'manual_score' => $awardedScore,
                    'is_finalized' => true,
                ]);

            $attempt = $this->gradeCalculationService->recalculateAttempt($review->attempt);
            $pendingReviews = $attempt->questionReviews()->whereNull('reviewed_at')->exists();

            $attempt->update([
                'status' => $pendingReviews ? 'pending_review' : 'checked',
                'checked_at' => $pendingReviews ? null : now(),
            ]);

            return $review->fresh('criterionScores');
        });
    }
}
