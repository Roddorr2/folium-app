<?php

namespace App\Domain\DTOs;

readonly class WorkDataDTO
{
    /**
     * @param int|string $id
     * @param string $title
     * @param string $author
     * @param string|null $abstract
     * @param int $availableCount
     * @param array<int, array{name: string, count: int}> $branches
     */
    public function __construct(
        public int|string $id,
        public string $title,
        public string $author,
        public ?string $abstract,
        public int $availableCount,
        public array $branches
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'abstract' => $this->abstract,
            'availableCount' => $this->availableCount,
            'branches' => $this->branches
        ];
    }
}
