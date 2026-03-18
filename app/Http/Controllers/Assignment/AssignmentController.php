<?php

namespace App\Http\Controllers\Assignment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Assignment\StoreAssignmentRequest;
use App\Models\Assignment;
use App\Models\AssessmentVersion;
use App\Models\TeacherGroup;
use App\Models\TeacherStudentLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                'teacherGroup',
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

        $groups = TeacherGroup::query()
            ->withCount('members')
            ->where('teacher_profile_id', $request->user()->teacherProfile?->id)
            ->where('status', 'active')
            ->get();

        return view('assignments.create', [
            'version' => $version,
            'students' => $students,
            'groups' => $groups,
        ]);
    }

    public function store(StoreAssignmentRequest $request): RedirectResponse
    {
        $this->authorize('create', Assignment::class);

        $version = AssessmentVersion::query()
            ->with('assessment')
            ->findOrFail($request->integer('assessment_version_id'));

        $this->authorize('view', $version->assessment);

        $targetType = $request->string('target_type')->value();
        $title = $request->string('title')->trim()->value() ?: $version->assessment->title;

        if ($targetType === 'student') {
            $assignment = Assignment::query()->create([
                'teacher_profile_id' => $request->user()->teacherProfile?->id,
                'assessment_version_id' => $version->id,
                'student_profile_id' => $request->integer('student_profile_id'),
                'title' => $title,
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

        $group = TeacherGroup::query()
            ->with('members')
            ->findOrFail($request->integer('teacher_group_id'));

        $studentIds = $group->members->pluck('student_profile_id')->unique()->values();

        if ($studentIds->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['teacher_group_id' => 'В выбранной группе пока нет учеников.']);
        }

        $assignmentsCreated = DB::transaction(function () use ($request, $version, $group, $studentIds, $title) {
            foreach ($studentIds as $studentProfileId) {
                Assignment::query()->create([
                    'teacher_profile_id' => $request->user()->teacherProfile?->id,
                    'assessment_version_id' => $version->id,
                    'teacher_group_id' => $group->id,
                    'student_profile_id' => $studentProfileId,
                    'title' => $title,
                    'instructions' => $request->input('instructions'),
                    'mode' => $request->string('mode')->value(),
                    'status' => 'published',
                    'starts_at' => $request->input('starts_at'),
                    'due_at' => $request->input('due_at'),
                    'max_attempts' => $request->integer('max_attempts'),
                    'is_published' => true,
                ]);
            }

            return $studentIds->count();
        });

        return redirect()
            ->route('teacher.groups.show', $group)
            ->with('status', "Для группы создано назначений: {$assignmentsCreated}.");
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
            'teacherGroup.gradeLevel',
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
