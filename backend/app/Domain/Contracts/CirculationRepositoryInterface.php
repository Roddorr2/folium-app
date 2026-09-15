<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use DateTimeInterface;

interface CirculationRepositoryInterface
{
    public function findItemByBarcode(string $barcode): ?array;
    
    public function findItemById(string|int $itemId): ?array;

    public function findLoanById(string|int $loanId): ?array;

    public function findActiveLoan(string|int $itemId, string|int $userId): ?array;
    
    public function createLoan(array $loanData): array;
    
    public function markReturned(string|int $loanId, DateTimeInterface $returnedAt): bool;

    public function updateItemStatus(string|int $itemId, string $status): bool;

    public function createRepatriationTransfer(array $transferData): array;
    
    public function countUserActiveLoans(string|int $userId): int;
    
    public function hasActiveReservations(string|int $workId): bool;
    
    public function getOldestPendingReservation(string|int $workId): ?array;
}
