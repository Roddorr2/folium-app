<?php

namespace App\Domain\DTOs;

readonly class IssueLoanDTO
{
    public function __construct(
        public string $barcode,
        public int|string $userId
    ) {}
}
