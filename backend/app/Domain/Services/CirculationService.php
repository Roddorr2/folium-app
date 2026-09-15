<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Contracts\CirculationRepositoryInterface;
use DomainException;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;

class CirculationService
{
    private const MAX_ACTIVE_LOANS = 5;
    private const LOAN_DAYS_DEFAULT = 14;

    public function __construct(
        private CirculationRepositoryInterface $repository
    ) {}

    /**
     * Create a new checkout loan for an item.
     */
    public function issueLoan(string $barcode, string|int $userId): array
    {
        return DB::transaction(function () use ($barcode, $userId) {
            $item = $this->repository->findItemByBarcode($barcode);
            if (!$item) {
                throw new DomainException("El ejemplar con código de barras {$barcode} no existe.");
            }

            if (($item['status'] ?? '') !== 'available') {
                throw new DomainException("El ejemplar no se encuentra disponible para préstamo (Estado: {$item['status']}).");
            }

            $activeLoansCount = $this->repository->countUserActiveLoans($userId);
            if ($activeLoansCount >= self::MAX_ACTIVE_LOANS) {
                throw new DomainException("El usuario ha alcanzado el límite máximo de " . self::MAX_ACTIVE_LOANS . " préstamos activos.");
            }

            $now = new DateTimeImmutable();
            $dueDate = $now->modify('+' . self::LOAN_DAYS_DEFAULT . ' days');

            return $this->repository->createLoan([
                'item_id' => $item['id'],
                'user_id' => $userId,
                'issued_at' => $now->format('Y-m-d H:i:s'),
                'due_date' => $dueDate->format('Y-m-d'),
                'status' => 'active'
            ]);
        });
    }

    /**
     * Process return of a loaned item with cross-branch repatriation handling (RF2.5 / US3-04).
     */
    public function returnLoan(string|int $loanId, string|int|null $receivingBranchId = null): array
    {
        return DB::transaction(function () use ($loanId, $receivingBranchId) {
            $now = new DateTimeImmutable();
            
            $loan = $this->repository->findLoanById($loanId);
            if (!$loan) {
                throw new DomainException("El préstamo ID {$loanId} no existe.");
            }

            $item = $this->repository->findItemById($loan['item_id']);
            if (!$item) {
                throw new DomainException("El ejemplar asociado al préstamo no fue encontrado.");
            }

            // 1. Close current loan
            $success = $this->repository->markReturned($loanId, $now);
            if (!$success) {
                throw new DomainException("No fue posible procesar la devolución del préstamo {$loanId}.");
            }

            // 2. Check cross-branch return (receiving branch != home branch)
            $homeBranchId = $item['home_branch_id'] ?? $item['branch_id'] ?? null;
            $isCrossBranchReturn = $receivingBranchId !== null 
                && $homeBranchId !== null 
                && (int)$receivingBranchId !== (int)$homeBranchId;

            if ($isCrossBranchReturn) {
                // RF2.5: Transición automática a 'in_transit' hacia la sede propietaria
                $this->repository->updateItemStatus($item['id'], 'in_transit');

                $repatriationTransfer = $this->repository->createRepatriationTransfer([
                    'item_id' => $item['id'],
                    'origin_branch_id' => $receivingBranchId,
                    'destination_branch_id' => $homeBranchId,
                    'status' => 'in_transit',
                    'reason' => 'repatriation'
                ]);

                return [
                    'loan_id' => $loanId,
                    'item_id' => $item['id'],
                    'returned_at' => $now->format('Y-m-d H:i:s'),
                    'status' => 'returned',
                    'item_status' => 'in_transit',
                    'repatriation_required' => true,
                    'receiving_branch_id' => $receivingBranchId,
                    'home_branch_id' => $homeBranchId,
                    'transfer' => $repatriationTransfer,
                    'message' => 'Devolución registrada. El ejemplar debe ser repatriado a su sede de origen.'
                ];
            }

            // Flujo habitual en sede propia: evaluar reservas FIFO o marcar disponible
            $workId = $item['work_id'] ?? $item['manifestation_id'] ?? $item['id'];
            $hasReservations = $this->repository->hasActiveReservations($workId);

            if ($hasReservations) {
                $this->repository->updateItemStatus($item['id'], 'reserved');
            } else {
                $this->repository->updateItemStatus($item['id'], 'available');
            }

            return [
                'loan_id' => $loanId,
                'item_id' => $item['id'],
                'returned_at' => $now->format('Y-m-d H:i:s'),
                'status' => 'returned',
                'item_status' => $hasReservations ? 'reserved' : 'available',
                'repatriation_required' => false,
                'message' => 'Devolución procesada exitosamente en sede de origen.'
            ];
        });
    }
}
