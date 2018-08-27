<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Service;

use VSV\GVQ_API\Quiz\Repositories\CounterRepository;
use VSV\GVQ_API\Quiz\Repositories\FinishedQuizRepository;
use VSV\GVQ_API\Quiz\Repositories\StartedQuizRepository;
use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;

class StatisticsService
{
    /**
     * @var StartedQuizRepository
     */
    private $startedQuizRepository;

    /**
     * @var FinishedQuizRepository
     */
    private $finishedQuizRepository;

    /**
     * @var StatisticsKey[]
     */
    private $statisticsKeys;

    /**
     * @param StartedQuizRepository $startedQuizRepository
     * @param FinishedQuizRepository $finishedQuizRepository
     */
    public function __construct(
        StartedQuizRepository $startedQuizRepository,
        FinishedQuizRepository $finishedQuizRepository
    ) {
        $this->startedQuizRepository = $startedQuizRepository;
        $this->finishedQuizRepository = $finishedQuizRepository;

        $this->statisticsKeys = StatisticsKey::getAllKeys();
    }

    /**
     * @return int[]
     */
    public function getStartedQuizCounts(): array
    {
        return $this->getCountsFromRepository($this->startedQuizRepository);
    }

    /**
     * @return int[]
     */
    public function getFinishedQuizCounts(): array
    {
        return $this->getCountsFromRepository($this->finishedQuizRepository);
    }

    /**
     * @param CounterRepository $counterRepository
     * @return array
     */
    private function getCountsFromRepository(CounterRepository $counterRepository): array
    {
        $totalNL = 0;
        $totalFR = 0;

        foreach ($this->statisticsKeys as $statisticsKey) {
            $key = $statisticsKey->toNative();
            $counts[$key] = $counterRepository->getCount($statisticsKey);
            if (substr($key, -2) === 'nl') {
                $totalNL += $counts[$key];
            } else {
                $totalFR += $counts[$key];
            }
        }

        $counts['total_nl'] = $totalNL;
        $counts['total_fr'] = $totalFR;

        return $counts;
    }
}
