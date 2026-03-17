<?php

namespace App\Services;

use App\Models\AssessmentVersion;
use App\Models\Attempt;

class GradeCalculationService
{
    public function gradeLabelForVersion(AssessmentVersion $version, float $score): ?string
    {
        $version->loadMissing(['gradingScale.ranges', 'assessment.gradingScales.ranges']);

        $scale = $version->gradingScale ?: $version->assessment->gradingScales->first();

        if (! $scale) {
            return null;
        }

        $range = $scale->ranges
            ->first(fn ($item) => $score >= (float) $item->min_score && $score <= (float) $item->max_score);

        return $range?->grade_label;
    }

    public function recalculateAttempt(Attempt $attempt): Attempt
    {
        $attempt->loadMissing([
            'questionAnswers',
            'questionReviews',
            'assignment.assessmentVersion.assessment.gradingScales.ranges',
            'assignment.assessmentVersion.gradingScale.ranges',
        ]);

        $autoScore = (float) $attempt->questionAnswers->sum('auto_score');
        $manualScore = (float) $attempt->questionReviews->sum('awarded_score');
        $finalScore = $autoScore + $manualScore;
        $gradeLabel = $this->gradeLabelForVersion($attempt->assignment->assessmentVersion, $finalScore);

        $attempt->update([
            'auto_score' => $autoScore,
            'manual_score' => $manualScore,
            'final_score' => $finalScore,
            'grade_label' => $gradeLabel,
        ]);

        return $attempt->fresh();
    }
}
