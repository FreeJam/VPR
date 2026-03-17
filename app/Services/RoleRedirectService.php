<?php

namespace App\Services;

use App\Models\User;

class RoleRedirectService
{
    public function routeNameFor(User $user): string
    {
        return match ($user->primaryRoleCode()) {
            'admin' => 'admin.dashboard',
            'teacher' => 'teacher.dashboard',
            'parent' => 'parent.dashboard',
            default => 'student.dashboard',
        };
    }
}
