<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkQueryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:255'],
            'branch_id' => ['nullable', 'integer']
        ];
    }
}
