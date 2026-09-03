<?php

namespace App\Domain\Contracts;

interface TransferStrategyInterface
{
    /**
     * Determine if this strategy applies to the given origin and destination branches.
     *
     * @param string|int $originBranchId
     * @param string|int $destinationBranchId
     * @return bool
     */
    public function supports(string|int $originBranchId, string|int $destinationBranchId): bool;

    /**
     * Execute transfer logic between branches.
     *
     * @param string|int $itemId
     * @param string|int $originBranchId
     * @param string|int $destinationBranchId
     * @return array<string, mixed>
     */
    public function executeTransfer(string|int $itemId, string|int $originBranchId, string|int $destinationBranchId): array;
}
