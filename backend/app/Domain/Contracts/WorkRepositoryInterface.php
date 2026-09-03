<?php

namespace App\Domain\Contracts;

interface WorkRepositoryInterface
{
    public function findById(string|int $id): ?array;

    public function getAvailabilityByBranch(string|int $workId): array;

    public function getUserCheckoutSubjects(string|int $userId): array;

    public function getWorksBySubjects(array $subjectIds, array $excludeWorkIds = [], int $limit = 10): array;
}
