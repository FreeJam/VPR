<?php

namespace App\Policies;

use App\Models\Assessment;
use App\Models\User;

class AssessmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin', 'teacher', 'student', 'parent');
    }

    public function view(User $user, Assessment $assessment): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('teacher') && $assessment->created_by === $user->id) {
            return true;
        }

        return $assessment->status === 'published';
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin', 'teacher');
    }

    public function update(User $user, Assessment $assessment): bool
    {
        return $user->hasRole('admin')
            || ($user->hasRole('teacher') && $assessment->created_by === $user->id);
    }

    public function delete(User $user, Assessment $assessment): bool
    {
        return $user->hasRole('admin');
    }
}
