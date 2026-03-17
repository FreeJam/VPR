<?php

namespace App\Policies;

use App\Models\ImportBatch;
use App\Models\User;

class ImportBatchPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin', 'teacher');
    }

    public function view(User $user, ImportBatch $importBatch): bool
    {
        return $user->hasRole('admin')
            || ($user->hasRole('teacher') && $importBatch->user_id === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin', 'teacher');
    }

    public function update(User $user, ImportBatch $importBatch): bool
    {
        return $this->view($user, $importBatch);
    }

    public function delete(User $user, ImportBatch $importBatch): bool
    {
        return $user->hasRole('admin');
    }
}
