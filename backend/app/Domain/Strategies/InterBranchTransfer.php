<?php

namespace App\Domain\Strategies;

use App\Domain\Contracts\TransferStrategyInterface;

class InterBranchTransfer implements TransferStrategyInterface
{
    public function supports(string|int $originBranchId, string|int $destinationBranchId): bool
    {
        return (string)$originBranchId !== (string)$destinationBranchId;
    }

    public function executeTransfer(string|int $itemId, string|int $originBranchId, string|int $destinationBranchId): array
    {
        return [
            'item_id' => $itemId,
            'origin_branch_id' => $originBranchId,
            'destination_branch_id' => $destinationBranchId,
            'status' => 'in_transit',
            'in_transit' => true,
            'message' => 'Solicitud de préstamo interbibliotecario registrada. El ejemplar pasa a tránsito entre sedes.'
        ];
    }
}
