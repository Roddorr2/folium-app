<?php

namespace App\Http\Requests;

use App\Domain\DTOs\IssueLoanDTO;
use Illuminate\Foundation\Http\FormRequest;

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
            'user_id' => ['required', 'integer'],
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
