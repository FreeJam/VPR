<?php

namespace App\Policies;

use App\Models\TeacherGroup;
use App\Models\User;

class TeacherGroupPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin', 'teacher');
    }

    public function view(User $user, TeacherGroup $teacherGroup): bool
    {
        return $user->hasRole('admin')
            || ($user->hasRole('teacher') && $teacherGroup->teacher_profile_id === $user->teacherProfile?->id);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('teacher');
    }

    public function update(User $user, TeacherGroup $teacherGroup): bool
    {
        return $user->hasRole('admin')
            || ($user->hasRole('teacher') && $teacherGroup->teacher_profile_id === $user->teacherProfile?->id);
    }
}
