<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class ReviewAttemptQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('teacher', 'admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'scores' => ['nullable', 'array'],
            'scores.*' => ['nullable', 'numeric', 'min:0'],
            'comment' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
