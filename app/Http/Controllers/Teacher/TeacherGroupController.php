<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\SaveTeacherGroupRequest;
use App\Models\GradeLevel;
use App\Models\TeacherGroup;
use App\Models\TeacherStudentLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TeacherGroupController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', TeacherGroup::class);

        return view('teacher.groups.index', [
            'groups' => TeacherGroup::query()
                ->with('gradeLevel')
                ->withCount('members')
                ->where('teacher_profile_id', $request->user()->teacherProfile?->id)
                ->latest()
                ->paginate(15),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', TeacherGroup::class);

        return view('teacher.groups.create', [
            'group' => new TeacherGroup(),
            'gradeLevels' => GradeLevel::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'students' => $this->teacherStudents($request),
            'selectedMembers' => [],
        ]);
    }

    public function store(SaveTeacherGroupRequest $request): RedirectResponse
    {
        $this->authorize('create', TeacherGroup::class);

        $group = DB::transaction(function () use ($request) {
            $group = TeacherGroup::query()->create([
                'teacher_profile_id' => $request->user()->teacherProfile?->id,
                'grade_level_id' => $request->input('grade_level_id'),
                'name' => $request->string('name')->value(),
                'description' => $request->input('description'),
                'status' => 'active',
            ]);

            $this->syncMembers($group, $request->input('member_ids', []));

            return $group;
        });

        return redirect()
            ->route('teacher.groups.show', $group)
            ->with('status', 'Группа создана.');
    }

    public function show(Request $request, TeacherGroup $teacherGroup): View
    {
        $this->authorize('view', $teacherGroup);

        $teacherGroup->load(['gradeLevel', 'members.studentProfile.user', 'members.studentProfile.gradeLevel']);

        return view('teacher.groups.show', [
            'group' => $teacherGroup,
            'gradeLevels' => GradeLevel::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'students' => $this->teacherStudents($request),
            'selectedMembers' => $teacherGroup->members->pluck('student_profile_id')->all(),
        ]);
    }

    public function update(SaveTeacherGroupRequest $request, TeacherGroup $teacherGroup): RedirectResponse
    {
        $this->authorize('update', $teacherGroup);

        DB::transaction(function () use ($request, $teacherGroup) {
            $teacherGroup->update([
                'grade_level_id' => $request->input('grade_level_id'),
                'name' => $request->string('name')->value(),
                'description' => $request->input('description'),
            ]);

            $this->syncMembers($teacherGroup, $request->input('member_ids', []));
        });

        return redirect()
            ->route('teacher.groups.show', $teacherGroup)
            ->with('status', 'Группа обновлена.');
    }

    private function teacherStudents(Request $request)
    {
        return TeacherStudentLink::query()
            ->with(['studentProfile.user', 'studentProfile.gradeLevel'])
            ->where('teacher_profile_id', $request->user()->teacherProfile?->id)
            ->where('status', 'approved')
            ->get()
            ->pluck('studentProfile')
            ->filter()
            ->values();
    }

    private function syncMembers(TeacherGroup $group, array $memberIds): void
    {
        $memberIds = collect($memberIds)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        $group->members()->whereNotIn('student_profile_id', $memberIds)->delete();

        $existing = $group->members()->pluck('student_profile_id');

        foreach ($memberIds->diff($existing) as $studentProfileId) {
            $group->members()->create([
                'student_profile_id' => $studentProfileId,
            ]);
        }
    }
}
