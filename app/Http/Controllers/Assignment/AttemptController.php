<?php

namespace App\Http\Controllers\Assignment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attempt\SaveAttemptRequest;
use App\Models\Assignment;
use App\Models\Attempt;
use App\Services\AttemptFlowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class AttemptController extends Controller
{
    public function start(Request $request, Assignment $assignment, AttemptFlowService $service): RedirectResponse
    {
        $this->authorize('start', $assignment);

        try {
            $attempt = $service->startOrResume($assignment, $request->user()->studentProfile);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['assignment' => $exception->getMessage()]);
        }

        return redirect()
            ->route('attempts.show', $attempt)
            ->with('status', 'Попытка открыта.');
    }

    public function show(Request $request, Attempt $attempt): View
    {
        $attempt->load([
            'assignment.assessmentVersion.assessment.subject',
            'assignment.assessmentVersion.assessment.gradeLevel',
            'assignment.assessmentVersion.sections.questions.questionType',
            'assignment.assessmentVersion.sections.questions.options',
            'assignment.assessmentVersion.sections.questions.answers',
            'assignment.assessmentVersion.sections.questions.rubric.criteria.levels',
            'questionAnswers',
            'questionReviews.criterionScores',
            'studentProfile.user',
        ]);

        $this->authorize('view', $attempt);

        return view('attempts.show', [
            'attempt' => $attempt,
            'responses' => $attempt->questionAnswers->keyBy('question_id'),
            'canEdit' => $request->user()->can('update', $attempt),
        ]);
    }

    public function update(SaveAttemptRequest $request, Attempt $attempt, AttemptFlowService $service): RedirectResponse
    {
        $attempt->load('assignment');
        $this->authorize('update', $attempt);

        $service->saveDraft($attempt, $request->validated('answers', []));

        return redirect()
            ->route('attempts.show', $attempt)
            ->with('status', 'Ответы сохранены.');
    }

    public function submit(SaveAttemptRequest $request, Attempt $attempt, AttemptFlowService $service): RedirectResponse
    {
        $attempt->load('assignment');
        $this->authorize('submit', $attempt);

        $service->submit($attempt, $request->validated('answers', []));

        return redirect()
            ->route('attempts.show', $attempt)
            ->with('status', 'Работа отправлена на проверку.');
    }
}
