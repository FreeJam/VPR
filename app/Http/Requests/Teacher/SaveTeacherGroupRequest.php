<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveTeacherGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('teacher') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'grade_level_id' => ['nullable', 'integer', Rule::exists('grade_levels', 'id')],
            'description' => ['nullable', 'string', 'max:255'],
            'member_ids' => ['nullable', 'array'],
            'member_ids.*' => [
                'integer',
                Rule::exists('teacher_student_links', 'student_profile_id')->where(function (Builder $query) {
                    $query
                        ->where('teacher_profile_id', $this->user()?->teacherProfile?->id)
                        ->where('status', 'approved');
                }),
            ],
        ];
    }
}
