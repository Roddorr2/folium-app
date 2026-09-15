<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Domain\DTOs\InitiateTransferDTO;

class InitiateTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_id' => ['required', 'integer'],
            'origin_branch_id' => ['required', 'integer'],
            'destination_branch_id' => ['required', 'integer']
        ];
    }

    public function toDTO(): InitiateTransferDTO
    {
        return new InitiateTransferDTO(
            itemId: $this->validated('item_id'),
            originBranchId: $this->validated('origin_branch_id'),
            destinationBranchId: $this->validated('destination_branch_id')
        );
    }
}
