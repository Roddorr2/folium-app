<?php

namespace App\Domain\DTOs;

readonly class InitiateTransferDTO
{
    public function __construct(
        public int|string $itemId,
        public int|string $originBranchId,
        public int|string $destinationBranchId
    ) {}
}
