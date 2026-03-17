<?php

namespace Database\Seeders;

use App\Models\GradeLevel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $gradeSixId = GradeLevel::query()->where('code', '6')->value('id');

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@vpr.local'],
            [
                'name' => 'Admin VPR',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'timezone' => 'Asia/Yekaterinburg',
                'is_active' => true,
            ],
        );
        $admin->assignRole('admin', true);

        $teacher = User::query()->updateOrCreate(
            ['email' => 'teacher@vpr.local'],
            [
                'name' => 'Teacher VPR',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'timezone' => 'Asia/Yekaterinburg',
                'is_active' => true,
            ],
        );
        $teacher->assignRole('teacher', true);
        $teacher->teacherProfile()->updateOrCreate(
            ['user_id' => $teacher->id],
            ['display_name' => 'Учитель Demo', 'organization_name' => 'VPR School'],
        );
        $teacher->teacherProfile->codes()->updateOrCreate(
            ['code' => 'DEMO-TEACHER'],
            ['status' => 'active'],
        );

        $student = User::query()->updateOrCreate(
            ['email' => 'student@vpr.local'],
            [
                'name' => 'Student VPR',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'timezone' => 'Asia/Yekaterinburg',
                'is_active' => true,
            ],
        );
        $student->assignRole('student', true);
        $student->studentProfile()->updateOrCreate(
            ['user_id' => $student->id],
            ['display_name' => 'Ученик Demo', 'grade_level_id' => $gradeSixId],
        );

        $parent = User::query()->updateOrCreate(
            ['email' => 'parent@vpr.local'],
            [
                'name' => 'Parent VPR',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'timezone' => 'Asia/Yekaterinburg',
                'is_active' => true,
            ],
        );
        $parent->assignRole('parent', true);
        $parent->parentProfile()->updateOrCreate(
            ['user_id' => $parent->id],
            ['display_name' => 'Родитель Demo'],
        );

        $teacher->teacherProfile->studentLinks()->updateOrCreate(
            ['student_profile_id' => $student->studentProfile->id],
            ['status' => 'approved', 'approved_at' => now()],
        );

        $parent->parentProfile->studentLinks()->updateOrCreate(
            ['student_profile_id' => $student->studentProfile->id],
            ['status' => 'approved'],
        );
    }
}
