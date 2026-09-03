<?php

namespace App\Infrastructure\Search;

use App\Domain\Contracts\SearcherInterface;

class DatabaseSearcher implements SearcherInterface
{
    public function search(string $query, array $filters = [], int $limit = 20, int $offset = 0): array
    {
        // Fallback search implementation using relational queries
        return [
            'engine' => 'database_fallback',
            'query' => $query,
            'limit' => $limit,
            'offset' => $offset,
            'hits' => []
        ];
    }

    public function indexWork(array $document): void
    {
        // Database works are already persisted in relational table
    }

    public function removeWork(string|int $workId): void
    {
        // Database works soft-deleted or removed directly
    }
}
