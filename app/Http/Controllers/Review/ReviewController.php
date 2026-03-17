<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\Controller;
use App\Http\Requests\Review\ReviewAttemptQuestionRequest;
use App\Models\AttemptQuestionReview;
use App\Services\ManualReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasRole('teacher', 'admin'), 403);

        $query = AttemptQuestionReview::query()
            ->with([
                'attempt.assignment.assessmentVersion.assessment.subject',
                'attempt.studentProfile.user',
                'question.rubric.criteria.levels',
            ])
            ->whereHas('attempt.assignment', function ($builder) use ($request) {
                if ($request->user()->hasRole('admin')) {
                    return;
                }

                $builder->where('teacher_profile_id', $request->user()->teacherProfile?->id);
            })
            ->orderByRaw('case when reviewed_at is null then 0 else 1 end')
            ->latest();

        return view('reviews.index', [
            'reviews' => $query->paginate(15),
        ]);
    }

    public function show(Request $request, AttemptQuestionReview $review): View
    {
        $review->load([
            'attempt.assignment.assessmentVersion.assessment.subject',
            'attempt.assignment.assessmentVersion.assessment.gradeLevel',
            'attempt.studentProfile.user',
            'attempt.questionAnswers',
            'question.questionType',
            'question.rubric.criteria.levels',
            'criterionScores',
        ]);

        $this->authorize('review', $review->attempt);

        return view('reviews.show', [
            'review' => $review,
            'response' => $review->attempt->questionAnswers->firstWhere('question_id', $review->question_id),
        ]);
    }

    public function update(
        ReviewAttemptQuestionRequest $request,
        AttemptQuestionReview $review,
        ManualReviewService $service
    ): RedirectResponse {
        $review->load(['attempt.assignment', 'question.rubric.criteria.levels']);
        $this->authorize('review', $review->attempt);

        $criteria = $review->question->rubric?->criteria ?? collect();
        $scores = [];

        foreach ($criteria as $criterion) {
            $value = (float) ($request->input("scores.{$criterion->id}") ?? 0);
            $scores[$criterion->id] = min(max($value, 0), (float) $criterion->max_points);
        }

        $service->applyReview($review, $scores, $request->input('comment'));

        return redirect()
            ->route('reviews.show', $review)
            ->with('status', 'Проверка сохранена.');
    }
}
