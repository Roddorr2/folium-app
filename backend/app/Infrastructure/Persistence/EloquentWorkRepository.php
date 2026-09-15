<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Contracts\WorkRepositoryInterface;
use App\Domain\DTOs\WorkDataDTO;
use App\Models\Work;

class EloquentWorkRepository implements WorkRepositoryInterface
{
    public function findById(string|int $id): ?array
    {
        $work = Work::with(['authors', 'subjects', 'expressions.language', 'expressions.manifestations.items.branch'])->find($id);
        return $work ? $work->toArray() : null;
    }

    /**
     * Get works formatted as WorkDataDTO items.
     *
     * @return array<int, WorkDataDTO>
     */
    public function getAllWorksFormatted(): array
    {
        $works = Work::with(['authors', 'subjects', 'expressions.language', 'expressions.manifestations.items.branch'])->get();

        return $works->map(function ($work) {
            $authorNames = $work->authors->pluck('name')->join(', ');

            $branchCounts = [];
            $totalAvailable = 0;

            foreach ($work->expressions as $expression) {
                foreach ($expression->manifestations as $manifestation) {
                    foreach ($manifestation->items as $item) {
                        if ($item->status === 'available' && $item->branch) {
                            $branchName = $item->branch->name;
                            $branchCounts[$branchName] = ($branchCounts[$branchName] ?? 0) + 1;
                            $totalAvailable++;
                        }
                    }
                }
            }

            $branches = [];
            foreach ($branchCounts as $name => $count) {
                $branches[] = [
                    'name' => $name,
                    'count' => $count
                ];
            }

            return new WorkDataDTO(
                id: $work->id,
                title: $work->title,
                author: $authorNames ?: 'Autor Desconocido',
                abstract: $work->abstract,
                availableCount: $totalAvailable,
                branches: $branches
            );
        })->all();
    }

    public function getAvailabilityByBranch(string|int $workId): array
    {
        return [];
    }

    public function getUserCheckoutSubjects(string|int $userId): array
    {
        return [];
    }

    public function getWorksBySubjects(array $subjectIds, array $excludeWorkIds = [], int $limit = 10): array
    {
        return [];
    }
}
