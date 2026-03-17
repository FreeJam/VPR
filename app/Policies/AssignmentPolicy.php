<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\User;

class AssignmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin', 'teacher', 'student');
    }

    public function view(User $user, Assignment $assignment): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('teacher') && $assignment->teacher_profile_id === $user->teacherProfile?->id) {
            return true;
        }

        return $user->hasRole('student') && $assignment->student_profile_id === $user->studentProfile?->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('teacher');
    }

    public function update(User $user, Assignment $assignment): bool
    {
        return $user->hasRole('admin')
            || ($user->hasRole('teacher') && $assignment->teacher_profile_id === $user->teacherProfile?->id);
    }

    public function start(User $user, Assignment $assignment): bool
    {
        return $user->hasRole('student')
            && $assignment->student_profile_id === $user->studentProfile?->id
            && $assignment->is_published;
    }
}
