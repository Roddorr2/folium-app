<?php

namespace App\Domain\Services;

use App\Domain\Contracts\TransferRepositoryInterface;
use App\Domain\Contracts\TransferStrategyInterface;
use DomainException;

class TransferService
{
    /**
     * @param TransferRepositoryInterface $repository
     * @param array<TransferStrategyInterface> $strategies
     */
    public function __construct(
        private TransferRepositoryInterface $repository,
        private array $strategies
    ) {}

    /**
     * Initiate transfer process for an item between branches.
     */
    public function initiateTransfer(string|int $itemId, string|int $originBranchId, string|int $destinationBranchId): array
    {
        $selectedStrategy = null;

        foreach ($this->strategies as $strategy) {
            if ($strategy instanceof TransferStrategyInterface && $strategy->supports($originBranchId, $destinationBranchId)) {
                $selectedStrategy = $strategy;
                break;
            }
        }

        if (!$selectedStrategy) {
            throw new DomainException("No se encontró una estrategia válida para la transferencia solicitada.");
        }

        $transferResult = $selectedStrategy->executeTransfer($itemId, $originBranchId, $destinationBranchId);

        $record = $this->repository->createTransferRequest([
            'item_id' => $itemId,
            'origin_branch_id' => $originBranchId,
            'destination_branch_id' => $destinationBranchId,
            'status' => $transferResult['status']
        ]);

        if ($transferResult['in_transit']) {
            $this->repository->updateItemBranchAndStatus($itemId, $originBranchId, 'in_transit');
        }

        return array_merge($record, ['detail' => $transferResult['message']]);
    }

    /**
     * Confirm receipt of item at destination branch.
     */
    public function confirmReceipt(string|int $transferId, string|int $destinationBranchId): array
    {
        $transfer = $this->repository->findById($transferId);
        if (!$transfer) {
            throw new DomainException("La solicitud de transferencia {$transferId} no existe.");
        }

        if ($transfer['status'] !== 'in_transit') {
            throw new DomainException("La transferencia debe estar en estado 'in_transit' para ser confirmada.");
        }

        $this->repository->updateTransferStatus($transferId, 'completed');
        $this->repository->updateItemBranchAndStatus($transfer['item_id'], $destinationBranchId, 'available');

        return [
            'transfer_id' => $transferId,
            'item_id' => $transfer['item_id'],
            'new_branch_id' => $destinationBranchId,
            'status' => 'available',
            'message' => 'Ejemplar recibido exitosamente en la sede de destino y disponible para préstamo.'
        ];
    }
}
