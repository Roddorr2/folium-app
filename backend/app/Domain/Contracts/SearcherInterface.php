<?php

namespace App\Domain\Contracts;

interface SearcherInterface
{
    /**
     * Perform full-text search with optional filters.
     *
     * @param string $query
     * @param array<string, mixed> $filters
     * @param int $limit
     * @param int $offset
     * @return array<string, mixed>
     */
    public function search(string $query, array $filters = [], int $limit = 20, int $offset = 0): array;

    /**
     * Index a single work entity.
     *
     * @param array<string, mixed> $document
     * @return void
     */
    public function indexWork(array $document): void;

    /**
     * Remove a work entity from the index.
     *
     * @param string|int $workId
     * @return void
     */
    public function removeWork(string|int $workId): void;
}
