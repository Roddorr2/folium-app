<?php

namespace App\Domain\Services;

use App\Domain\Contracts\WorkRepositoryInterface;
use App\Domain\Contracts\SearcherInterface;

class RecommendationService
{
    public function __construct(
        private WorkRepositoryInterface $workRepository,
        private SearcherInterface $searcher
    ) {}

    /**
     * Calculate personalized recommendations for a user based on checkout subjects.
     *
     * @param string|int $userId
     * @param int $limit
     * @return array<int, array<string, mixed>>
     */
    public function getRecommendationsForUser(string|int $userId, int $limit = 10): array
    {
        $frequentSubjectIds = $this->workRepository->getUserCheckoutSubjects($userId);

        if (empty($frequentSubjectIds)) {
            // Fallback to general search engine query for trending works
            $searchResults = $this->searcher->search('popular', [], $limit, 0);
            return $searchResults['hits'] ?? [];
        }

        return $this->workRepository->getWorksBySubjects($frequentSubjectIds, [], $limit);
    }
}
