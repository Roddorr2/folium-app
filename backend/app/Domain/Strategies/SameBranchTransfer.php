<?php

namespace App\Domain\Strategies;

use App\Domain\Contracts\TransferStrategyInterface;

class SameBranchTransfer implements TransferStrategyInterface
{
    public function supports(string|int $originBranchId, string|int $destinationBranchId): bool
    {
        return (string)$originBranchId === (string)$destinationBranchId;
    }

    public function executeTransfer(string|int $itemId, string|int $originBranchId, string|int $destinationBranchId): array
    {
        return [
            'item_id' => $itemId,
            'origin_branch_id' => $originBranchId,
            'destination_branch_id' => $destinationBranchId,
            'status' => 'completed',
            'in_transit' => false,
            'message' => 'Ejemplar reubicado dentro de la misma sede sin tránsito interbibliotecario.'
        ];
    }
}
