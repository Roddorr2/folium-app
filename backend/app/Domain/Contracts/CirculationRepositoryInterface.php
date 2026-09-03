<?php

namespace App\Domain\Contracts;

interface CirculationRepositoryInterface
{
    public function findItemByBarcode(string $barcode): ?array;
    
    public function findActiveLoan(string|int $itemId, string|int $userId): ?array;
    
    public function createLoan(array $loanData): array;
    
    public function markReturned(string|int $loanId, \DateTimeInterface $returnedAt): bool;
    
    public function countUserActiveLoans(string|int $userId): int;
    
    public function hasActiveReservations(string|int $workId): bool;
    
    public function getOldestPendingReservation(string|int $workId): ?array;
}
