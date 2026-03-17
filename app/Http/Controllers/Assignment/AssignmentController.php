<?php

namespace App\Http\Controllers\Assignment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Assignment\StoreAssignmentRequest;
use App\Models\Assignment;
use App\Models\AssessmentVersion;
use App\Models\TeacherStudentLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Assignment::class);

        $query = Assignment::query()
            ->with([
                'assessmentVersion.assessment.subject',
                'assessmentVersion.assessment.gradeLevel',
                'studentProfile.user',
                'teacherProfile.user',
                'attempts' => fn ($attempts) => $attempts->latest('attempt_number'),
            ])
            ->latest();

        if ($request->user()->hasRole('teacher')) {
            $query->where('teacher_profile_id', $request->user()->teacherProfile?->id);
        } elseif ($request->user()->hasRole('student')) {
            $query->where('student_profile_id', $request->user()->studentProfile?->id);
        }

        return view('assignments.index', [
            'assignments' => $query->paginate(15),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Assignment::class);

        $version = AssessmentVersion::query()
            ->with(['assessment.subject', 'assessment.gradeLevel'])
            ->findOrFail($request->integer('version'));

        $this->authorize('view', $version->assessment);

        $students = TeacherStudentLink::query()
            ->with(['studentProfile.user', 'studentProfile.gradeLevel'])
            ->where('teacher_profile_id', $request->user()->teacherProfile?->id)
            ->where('status', 'approved')
            ->get()
            ->pluck('studentProfile')
            ->filter();

        return view('assignments.create', [
            'version' => $version,
            'students' => $students,
        ]);
    }

    public function store(StoreAssignmentRequest $request): RedirectResponse
    {
        $this->authorize('create', Assignment::class);

        $version = AssessmentVersion::query()
            ->with('assessment')
            ->findOrFail($request->integer('assessment_version_id'));

        $this->authorize('view', $version->assessment);

        $assignment = Assignment::query()->create([
            'teacher_profile_id' => $request->user()->teacherProfile?->id,
            'assessment_version_id' => $version->id,
            'student_profile_id' => $request->integer('student_profile_id'),
            'title' => $request->string('title')->trim()->value() ?: $version->assessment->title,
            'instructions' => $request->input('instructions'),
            'mode' => $request->string('mode')->value(),
            'status' => 'published',
            'starts_at' => $request->input('starts_at'),
            'due_at' => $request->input('due_at'),
            'max_attempts' => $request->integer('max_attempts'),
            'is_published' => true,
        ]);

        return redirect()
            ->route('assignments.show', $assignment)
            ->with('status', 'Назначение создано и опубликовано.');
    }

    public function show(Request $request, Assignment $assignment): View
    {
        $this->authorize('view', $assignment);

        $assignment->load([
            'assessmentVersion.assessment.subject',
            'assessmentVersion.assessment.gradeLevel',
            'assessmentVersion.sections.questions.questionType',
            'studentProfile.user',
            'teacherProfile.user',
            'attempts.studentProfile.user',
        ]);

        $latestAttempt = null;

        if ($request->user()->hasRole('student')) {
            $latestAttempt = $assignment->attempts
                ->where('student_profile_id', $request->user()->studentProfile?->id)
                ->sortByDesc('attempt_number')
                ->first();
        }

        return view('assignments.show', [
            'assignment' => $assignment,
            'latestAttempt' => $latestAttempt,
        ]);
    }
}
