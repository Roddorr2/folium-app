<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Contracts\TransferRepositoryInterface;
use App\Domain\Contracts\TransferStrategyInterface;
use App\Domain\Contracts\NotifierInterface;
use App\Domain\Events\ItemLostInTransitEvent;
use DomainException;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;

class TransferService
{
    /**
     * @param TransferRepositoryInterface $repository
     * @param array<TransferStrategyInterface> $strategies
     * @param NotifierInterface|null $notifier
     */
    public function __construct(
        private TransferRepositoryInterface $repository,
        private array $strategies,
        private ?NotifierInterface $notifier = null
    ) {}

    /**
     * Initiate transfer process for an item between branches.
     */
    public function initiateTransfer(string|int $itemId, string|int $originBranchId, string|int $destinationBranchId): array
    {
        return DB::transaction(function () use ($itemId, $originBranchId, $destinationBranchId) {
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
        });
    }

    /**
     * Confirm receipt of item at destination branch.
     */
    public function confirmReceipt(string|int $transferId, string|int $destinationBranchId): array
    {
        return DB::transaction(function () use ($transferId, $destinationBranchId) {
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
        });
    }

    /**
     * Resolution of ILL transit failures and item losses (RF5.6 / US4-04).
     */
    public function markAsLost(string|int $transferId, string $reason, string|int $reportedByUserId): array
    {
        return DB::transaction(function () use ($transferId, $reason, $reportedByUserId) {
            $transfer = $this->repository->findById($transferId);
            if (!$transfer) {
                throw new DomainException("La solicitud de transferencia ID {$transferId} no fue encontrada.");
            }

            if (!in_array($transfer['status'], ['in_transit', 'pending'], true)) {
                throw new DomainException("Solo se pueden declarar extravíos para transferencias en estado 'in_transit' o 'pending'.");
            }

            $now = (new DateTimeImmutable())->format('Y-m-d H:i:s');

            // 1. Transition Item status to 'lost'
            $this->repository->updateItemBranchAndStatus(
                $transfer['item_id'],
                $transfer['origin_branch_id'],
                'lost'
            );

            // 2. Update transfer log to 'failed' with audit detail
            $this->repository->updateTransferStatusWithAudit($transferId, 'failed', [
                'reason' => $reason,
                'reported_by_user_id' => $reportedByUserId,
                'resolved_at' => $now
            ]);

            // 3. Notify affected reader if reservation was associated
            $affectedUserId = $transfer['user_id'] ?? $transfer['requested_by_user_id'] ?? null;
            if ($affectedUserId && $this->notifier) {
                $this->notifier->notifyUser(
                    $affectedUserId,
                    "Tu solicitud de préstamo intersede fue cancelada debido a una incidencia en transporte. El ejemplar fue reportado como extraviado.",
                    ['transfer_id' => $transferId, 'item_id' => $transfer['item_id'], 'reason' => $reason]
                );
            }

            // 4. Dispatch domain event
            ItemLostInTransitEvent::dispatch(
                $transferId,
                $transfer['item_id'],
                $affectedUserId,
                $reason,
                $now
            );

            return [
                'transfer_id' => $transferId,
                'item_id' => $transfer['item_id'],
                'item_status' => 'lost',
                'transfer_status' => 'failed',
                'reason' => $reason,
                'reported_by_user_id' => $reportedByUserId,
                'resolved_at' => $now,
                'message' => 'Incidencia en transporte procesada. El ejemplar ha sido marcado como extraviado.'
            ];
        });
    }
}
