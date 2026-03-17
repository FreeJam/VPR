<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['code' => 'admin', 'name' => 'Administrator', 'description' => 'Platform administrator'],
            ['code' => 'teacher', 'name' => 'Teacher', 'description' => 'Teacher workspace'],
            ['code' => 'student', 'name' => 'Student', 'description' => 'Student workspace'],
            ['code' => 'parent', 'name' => 'Parent', 'description' => 'Parent workspace'],
        ] as $role) {
            Role::query()->updateOrCreate(
                ['code' => $role['code']],
                $role + ['is_system' => true],
            );
        }
    }
}
