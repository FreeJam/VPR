<?php

namespace App\Http\Requests\Import;

use App\Models\ImportBatch;
use Illuminate\Foundation\Http\FormRequest;

class UploadImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', ImportBatch::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'max:10240'],
        ];
    }
}
