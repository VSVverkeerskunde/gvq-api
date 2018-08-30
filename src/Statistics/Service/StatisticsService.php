<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Service;

use VSV\GVQ_API\Partner\Models\Partner;
use VSV\GVQ_API\Partner\Repositories\PartnerRepository;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Quiz\Repositories\FinishedQuizRepository;
use VSV\GVQ_API\Quiz\Repositories\StartedQuizRepository;
use VSV\GVQ_API\Quiz\Repositories\StatisticsRepository;
use VSV\GVQ_API\Quiz\Repositories\UniqueParticipantRepository;
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
     * @var UniqueParticipantRepository
     */
    private $uniqueParticipantRepository;

    /**
     * @var PartnerRepository
     */
    private $partnerRepository;

    /**
     * @var StatisticsKey[]
     */
    private $statisticsKeys;

    /**
     * @var Year
     */
    private $year;

    /**
     * @param StartedQuizRepository $startedQuizRepository
     * @param FinishedQuizRepository $finishedQuizRepository
     * @param UniqueParticipantRepository $uniqueParticipantRepository
     * @param PartnerRepository $partnerRepository
     * @param Year $year
     */
    public function __construct(
        StartedQuizRepository $startedQuizRepository,
        FinishedQuizRepository $finishedQuizRepository,
        UniqueParticipantRepository $uniqueParticipantRepository,
        PartnerRepository $partnerRepository,
        Year $year
    ) {
        $this->startedQuizRepository = $startedQuizRepository;
        $this->finishedQuizRepository = $finishedQuizRepository;
        $this->uniqueParticipantRepository = $uniqueParticipantRepository;
        $this->partnerRepository = $partnerRepository;
        $this->year = $year;

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
     * @return int[]
     */
    public function getUniqueParticipantCounts(): array
    {
        return $this->getCountsFromRepository($this->uniqueParticipantRepository);
    }

    /**
     * @return array|null
     */
    public function getUniqueParticipantCountsForPartners(): ?array
    {
        $partners = $this->partnerRepository->getAllByYear($this->year);

        if (empty($partners)) {
            return null;
        }

        $counts = [];

        foreach ($partners as $partner) {
            /** @var Partner $partner */
            $nlCount = $this->uniqueParticipantRepository->getPartnerCount(
                new StatisticsKey(StatisticsKey::PARTNER_NL),
                $partner
            );

            $frCount = $this->uniqueParticipantRepository->getPartnerCount(
                new StatisticsKey(StatisticsKey::PARTNER_FR),
                $partner
            );

            $totalCount = $nlCount + $frCount;

            $counts[$partner->getName()->toNative()]['nl'] = $nlCount;
            $counts[$partner->getName()->toNative()]['fr'] = $frCount;
            $counts[$partner->getName()->toNative()]['total'] = $totalCount;
        }

        return $counts;
    }

    /**
     * @param StatisticsRepository $statisticsRepository
     * @return array
     */
    private function getCountsFromRepository(StatisticsRepository $statisticsRepository): array
    {
        $totalNL = 0;
        $totalFR = 0;
        $counts = [];

        foreach ($this->statisticsKeys as $statisticsKey) {
            $key = $statisticsKey->toNative();
            $counts[$key] = $statisticsRepository->getCount($statisticsKey);
            if ($statisticsKey->getLanguage() === 'nl') {
                $totalNL += $counts[$key];
            } else {
                $totalFR += $counts[$key];
            }

            $totalKey = substr($key, 0, -2).'total';

            if (key_exists($totalKey, $counts)) {
                $counts[$totalKey] += $counts[$key];
            } else {
                $counts[$totalKey] = $counts[$key];
            }
        }

        $counts['quiz_total_nl'] = $totalNL - $counts['cup_nl'];
        $counts['quiz_total_fr'] = $totalFR - $counts['cup_fr'];
        $counts['quiz_total'] = $counts['quiz_total_nl'] + $counts['quiz_total_fr'];
        $counts['total_nl'] = $totalNL;
        $counts['total_fr'] = $totalFR;
        $counts['total'] = $totalNL + $totalFR;


        return $counts;
    }
}
