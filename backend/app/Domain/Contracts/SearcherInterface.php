<?php

namespace App\Domain\Contracts;

interface SearcherInterface
{
    /**
     * Perform full-text search with optional filters.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function search(string $query, array $filters = [], int $limit = 20, int $offset = 0): array;

    /**
     * Index a single work entity.
     *
     * @param  array<string, mixed>  $document
     */
    public function indexWork(array $document): void;

    /**
     * Remove a work entity from the index.
     */
    public function removeWork(string|int $workId): void;
}
