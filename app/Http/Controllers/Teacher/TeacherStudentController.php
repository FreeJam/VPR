<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\GroupMember;
use App\Models\TeacherStudentLink;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class TeacherStudentController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasRole('teacher'), 403);

        $teacherProfileId = $request->user()->teacherProfile?->id;

        $links = TeacherStudentLink::query()
            ->with(['studentProfile.user', 'studentProfile.gradeLevel'])
            ->where('teacher_profile_id', $teacherProfileId)
            ->where('status', 'approved')
            ->latest('approved_at')
            ->get();

        $groupCounts = GroupMember::query()
            ->selectRaw('student_profile_id, COUNT(*) as aggregate')
            ->whereHas('teacherGroup', fn ($query) => $query->where('teacher_profile_id', $teacherProfileId))
            ->groupBy('student_profile_id')
            ->pluck('aggregate', 'student_profile_id');

        $students = $links->map(function (TeacherStudentLink $link) use ($groupCounts): array {
            $studentProfile = $link->studentProfile;

            return [
                'link' => $link,
                'student' => $studentProfile,
                'groups_count' => (int) ($groupCounts[$link->student_profile_id] ?? 0),
                'assignments_count' => (int) ($studentProfile
                    ? $studentProfile->assignments()->where('teacher_profile_id', $link->teacher_profile_id)->count()
                    : 0),
            ];
        });

        return view('teacher.students.index', [
            'students' => $students,
        ]);
    }
}
