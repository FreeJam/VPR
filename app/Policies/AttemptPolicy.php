<?php

namespace App\Policies;

use App\Models\Attempt;
use App\Models\User;

class AttemptPolicy
{
    public function view(User $user, Attempt $attempt): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('teacher') && $attempt->assignment->teacher_profile_id === $user->teacherProfile?->id) {
            return true;
        }

        return $user->hasRole('student') && $attempt->student_profile_id === $user->studentProfile?->id;
    }

    public function update(User $user, Attempt $attempt): bool
    {
        return $user->hasRole('student')
            && $attempt->student_profile_id === $user->studentProfile?->id
            && $attempt->status === 'in_progress';
    }

    public function submit(User $user, Attempt $attempt): bool
    {
        return $this->update($user, $attempt);
    }

    public function review(User $user, Attempt $attempt): bool
    {
        return $user->hasRole('admin')
            || ($user->hasRole('teacher') && $attempt->assignment->teacher_profile_id === $user->teacherProfile?->id);
    }
}
