<?php

namespace App\Infrastructure\Search;

use App\Domain\Contracts\SearcherInterface;

class MeilisearchSearcher implements SearcherInterface
{
    public function search(string $query, array $filters = [], int $limit = 20, int $offset = 0): array
    {
        // Infrastructure implementation wrapper over Meilisearch / Laravel Scout
        return [
            'engine' => 'meilisearch',
            'query' => $query,
            'limit' => $limit,
            'offset' => $offset,
            'hits' => []
        ];
    }

    public function indexWork(array $document): void
    {
        // Index work document into Meilisearch index
    }

    public function removeWork(string|int $workId): void
    {
        // Remove work document from Meilisearch index
    }
}
