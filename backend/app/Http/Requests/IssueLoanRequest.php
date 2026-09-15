<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Domain\DTOs\IssueLoanDTO;

class IssueLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'barcode' => ['required', 'string', 'max:255'],
            'user_id' => ['required', 'integer']
        ];
    }

    public function toDTO(): IssueLoanDTO
    {
        return new IssueLoanDTO(
            barcode: $this->validated('barcode'),
            userId: $this->validated('user_id')
        );
    }
}
