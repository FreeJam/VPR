<?php

namespace App\Http\Requests\Assignment;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('teacher') ?? false;
    }

    public function rules(): array
    {
        return [
            'assessment_version_id' => ['required', 'integer', Rule::exists('assessment_versions', 'id')],
            'target_type' => ['required', 'string', Rule::in(['student', 'group'])],
            'student_profile_id' => [
                'nullable',
                'integer',
                Rule::requiredIf($this->input('target_type') === 'student'),
                Rule::exists('teacher_student_links', 'student_profile_id')->where(function (Builder $query) {
                    $query
                        ->where('teacher_profile_id', $this->user()?->teacherProfile?->id)
                        ->where('status', 'approved');
                }),
            ],
            'teacher_group_id' => [
                'nullable',
                'integer',
                Rule::requiredIf($this->input('target_type') === 'group'),
                Rule::exists('teacher_groups', 'id')->where(function (Builder $query) {
                    $query->where('teacher_profile_id', $this->user()?->teacherProfile?->id);
                }),
            ],
            'title' => ['nullable', 'string', 'max:255'],
            'instructions' => ['nullable', 'string'],
            'mode' => ['required', 'string', Rule::in(['training', 'homework', 'exam'])],
            'starts_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'max_attempts' => ['required', 'integer', 'min:1', 'max:10'],
        ];
    }
}
