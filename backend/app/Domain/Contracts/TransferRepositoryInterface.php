<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

interface TransferRepositoryInterface
{
    public function createTransferRequest(array $transferData): array;

    public function updateTransferStatus(string|int $transferId, string $status): bool;

    public function updateTransferStatusWithAudit(string|int $transferId, string $status, array $auditData): bool;

    public function findById(string|int $transferId): ?array;

    public function updateItemBranchAndStatus(string|int $itemId, string|int $newBranchId, string $newStatus): bool;
}
