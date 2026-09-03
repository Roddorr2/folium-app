<?php

namespace App\Domain\Services;

use App\Domain\Contracts\CirculationRepositoryInterface;
use DomainException;
use DateTimeImmutable;

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
    }

    /**
     * Process return of a loaned item.
     */
    public function returnLoan(string|int $loanId): array
    {
        $now = new DateTimeImmutable();
        $success = $this->repository->markReturned($loanId, $now);

        if (!$success) {
            throw new DomainException("No fue posible procesar la devolución del préstamo {$loanId}.");
        }

        return [
            'loan_id' => $loanId,
            'returned_at' => $now->format('Y-m-d H:i:s'),
            'status' => 'returned'
        ];
    }
}
