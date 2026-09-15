<?php

declare(strict_types=1);

namespace App\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;

class ItemLostInTransitEvent
{
    use Dispatchable;

    public function __construct(
        public readonly string|int $transferId,
        public readonly string|int $itemId,
        public readonly string|int|null $affectedUserId,
        public readonly string $reason,
        public readonly string $reportedAt
    ) {}
}
