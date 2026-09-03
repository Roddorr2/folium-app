<?php

namespace App\Domain\Contracts;

interface TransferRepositoryInterface
{
    public function createTransferRequest(array $transferData): array;

    public function updateTransferStatus(string|int $transferId, string $status): bool;

    public function findById(string|int $transferId): ?array;

    public function updateItemBranchAndStatus(string|int $itemId, string|int $newBranchId, string $newStatus): bool;
}
