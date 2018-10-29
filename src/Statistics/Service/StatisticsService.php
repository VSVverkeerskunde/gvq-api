<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Service;

use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Company\Models\Companies;
use VSV\GVQ_API\Partner\Models\Partner;
use VSV\GVQ_API\Partner\Repositories\PartnerRepository;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
use VSV\GVQ_API\Statistics\Repositories\DetailedTopScoreRepository;
use VSV\GVQ_API\Statistics\Repositories\FinishedQuizRepository;
use VSV\GVQ_API\Statistics\Repositories\StartedQuizRepository;
use VSV\GVQ_API\Statistics\Repositories\TopScoreRepository;
use VSV\GVQ_API\Statistics\Repositories\UniqueParticipantRepository;
use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;

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
     * @var DetailedTopScoreRepository
     */
    private $detailedTopScoreRepository;

    /**
     * @var TopScoreRepository
     */
    private $topScoreRepository;

    /**
     * @var StatisticsKey[]
     */
    private $statisticsKeys;

    /**
     * @var int[]
     */
    private $uniqueParticipantsCount;

    /**
     * @var int[]
     */
    private $passedUniqueParticipantsCount;

    /**
     * @param StartedQuizRepository $startedQuizRepository
     * @param FinishedQuizRepository $finishedQuizRepository
     * @param UniqueParticipantRepository $uniqueParticipantRepository
     * @param PartnerRepository $partnerRepository
     * @param DetailedTopScoreRepository $detailedTopScoreRepository
     * @param TopScoreRepository $topScoreRepository
     */
    public function __construct(
        StartedQuizRepository $startedQuizRepository,
        FinishedQuizRepository $finishedQuizRepository,
        UniqueParticipantRepository $uniqueParticipantRepository,
        PartnerRepository $partnerRepository,
        DetailedTopScoreRepository $detailedTopScoreRepository,
        TopScoreRepository $topScoreRepository
    ) {
        $this->startedQuizRepository = $startedQuizRepository;
        $this->finishedQuizRepository = $finishedQuizRepository;
        $this->uniqueParticipantRepository = $uniqueParticipantRepository;
        $this->partnerRepository = $partnerRepository;
        $this->detailedTopScoreRepository = $detailedTopScoreRepository;
        $this->topScoreRepository = $topScoreRepository;

        $this->statisticsKeys = StatisticsKey::getAllKeys();
    }

    /**
     * @return int[]
     */
    public function getStartedQuizCounts(): array
    {
        return $this->getCountsFromRepository(
            function (StatisticsKey $statisticsKey) {
                return $this->startedQuizRepository->getCount($statisticsKey);
            }
        );
    }

    /**
     * @return int[]
     */
    public function getFinishedQuizCounts(): array
    {
        return $this->getCountsFromRepository(
            function (StatisticsKey $statisticsKey) {
                return $this->finishedQuizRepository->getCount($statisticsKey);
            }
        );
    }

    /**
     * @return int[]
     */
    public function getUniqueParticipantCounts(): array
    {
        if ($this->uniqueParticipantsCount === null) {
            $this->uniqueParticipantsCount = $this->getUniqueCountsFromRepository(
                function (StatisticsKey $statisticsKey) {
                    return $this->uniqueParticipantRepository->getCount($statisticsKey);
                }
            );
        }

        return $this->uniqueParticipantsCount;
    }

    /**
     * @return int[]
     */
    public function getPassedUniqueParticipantCounts(): array
    {
        if ($this->passedUniqueParticipantsCount === null) {
            $this->passedUniqueParticipantsCount = $this->getCountsFromRepository(
                function (StatisticsKey $statisticsKey) {
                    return $this->uniqueParticipantRepository->getPassedCount($statisticsKey);
                }
            );
        }

        return $this->passedUniqueParticipantsCount;
    }

    /**
     * @return float[]
     */
    public function getPassedUniqueParticipantPercentages(): array
    {
        $uniqueParticipantsCounts = $this->getUniqueParticipantCounts();
        $passedUniqueParticipantCounts = $this->getPassedUniqueParticipantCounts();

        $passedUniqueParticipantPercentage = [];
        foreach ($uniqueParticipantsCounts as $key => $uniqueParticipantsCount) {
            if (empty($uniqueParticipantsCounts[$key])) {
                $passedUniqueParticipantPercentage[$key] = 0;
            } else {
                $passedUniqueParticipantPercentage[$key] = round(
                    (float)$passedUniqueParticipantCounts[$key] / (float)$uniqueParticipantsCounts[$key],
                    2
                ) * 100;
            }
        }

        return $passedUniqueParticipantPercentage;
    }

    /**
     * @return float[]
     */
    public function getDetailedTopScoreAverages(): array
    {
        $averages = [];

        foreach ($this->statisticsKeys as $statisticsKey) {
            $average = $this->detailedTopScoreRepository->getAverageByKey($statisticsKey);
            $averages[$statisticsKey->toNative()] = $average->toNative();
        }

        $averages['individual_total'] = $this->detailedTopScoreRepository->getAverageByChannel(
            new QuizChannel(QuizChannel::INDIVIDUAL)
        )->toNative();
        $averages['company_total'] = $this->detailedTopScoreRepository->getAverageByChannel(
            new QuizChannel(QuizChannel::COMPANY)
        )->toNative();
        $averages['partner_total'] = $this->detailedTopScoreRepository->getAverageByChannel(
            new QuizChannel(QuizChannel::PARTNER)
        )->toNative();
        $averages['cup_total'] = $this->detailedTopScoreRepository->getAverageByChannel(
            new QuizChannel(QuizChannel::CUP)
        )->toNative();

        $averages['quiz_total_nl'] = $this->detailedTopScoreRepository->getQuizAverage(
            new Language('nl')
        )->toNative();
        $averages['quiz_total_fr'] = $this->detailedTopScoreRepository->getQuizAverage(
            new Language('fr')
        )->toNative();
        $averages['quiz_total'] = $this->detailedTopScoreRepository->getQuizAverage(
            null
        )->toNative();

        $averages['total_nl'] = $this->detailedTopScoreRepository->getAverageByLanguage(
            new Language('nl')
        )->toNative();
        $averages['total_fr'] = $this->detailedTopScoreRepository->getAverageByLanguage(
            new Language('fr')
        )->toNative();

        $averages['total'] = $this->detailedTopScoreRepository->getTotalAverage()->toNative();

        foreach ($averages as $key => $average) {
            $averages[$key] = round($averages[$key], 2);
        }

        return $averages;
    }

    /**
     * @param Year $year
     * @return array|null
     */
    public function getUniqueParticipantCountsForPartnersByYear(Year $year): ?array
    {
        $partners = $this->partnerRepository->getAllByYear($year);

        if (empty($partners)) {
            return null;
        }

        $counts = [];

        foreach ($partners as $partner) {
            /** @var Partner $partner */
            $nlCount = $this->uniqueParticipantRepository->getCountForPartner(
                new StatisticsKey(StatisticsKey::PARTNER_NL),
                $partner
            );

            $frCount = $this->uniqueParticipantRepository->getCountForPartner(
                new StatisticsKey(StatisticsKey::PARTNER_FR),
                $partner
            );

            $totalCount = $nlCount + $frCount;

            $counts[$partner->getName()->toNative()][Language::NL] = $nlCount;
            $counts[$partner->getName()->toNative()][Language::FR] = $frCount;
            $counts[$partner->getName()->toNative()]['total'] = $totalCount;
        }

        return $counts;
    }

    /**
     * @return Companies|null
     */
    public function getTopCompanies(): ?Companies
    {
        return $this->topScoreRepository->getTopCompanies(new NaturalNumber(10));
    }

    /**
     * @param callable $countFunction
     * @return array
     */
    private function getCountsFromRepository(callable $countFunction): array
    {
        $totalNL = 0;
        $totalFR = 0;
        $counts = [];

        foreach ($this->statisticsKeys as $statisticsKey) {
            $key = $statisticsKey->toNative();

            $counts[$key] = $countFunction($statisticsKey);

            if ($statisticsKey->getLanguage()->toNative() === Language::NL) {
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

    /**
     * @param callable $countFunction
     * @return array
     */
    private function getUniqueCountsFromRepository(callable $countFunction): array
    {
        $counts = [];

        $tmpStatisticsKey = new StatisticsKey(StatisticsKey::OVERALL_TOT);

        foreach ($tmpStatisticsKey->getAllowedValues() as $statisticsKey) {
            $counts[$statisticsKey] = $countFunction(new StatisticsKey($statisticsKey));
        }

        return $counts;
    }
}
