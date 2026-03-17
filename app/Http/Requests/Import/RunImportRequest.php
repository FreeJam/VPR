<?php

namespace App\Http\Requests\Import;

use Illuminate\Foundation\Http\FormRequest;

class RunImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'confirm' => ['required', 'accepted'],
        ];
    }
}
