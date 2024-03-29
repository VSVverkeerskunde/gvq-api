<?php

namespace VSV\GVQ_API\Statistics\Service;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Contest\Repositories\ContestParticipationRepository;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;
use VSV\GVQ_API\Statistics\Models\RankedCompanyParticipant;
use VSV\GVQ_API\Statistics\Models\TopScore;
use VSV\GVQ_API\Statistics\Repositories\TopScoreRepository;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;
use function array_map;

class CompanyParticipantRanker
{
    /**
     * @var TopScoreRepository
     */
    private $topScoreRepository;

    /**
     * @var ContestParticipationRepository
     */
    private $contestParticipationRepository;

    /**
     * @var StatisticsKey
     */
    private $statisticsKey;

    /**
     * @var StatisticsService
     */
    private $statisticsService;

    public function __construct(
        TopScoreRepository $topScoreRepository,
        ContestParticipationRepository $contestParticipationRepository,
        StatisticsService $statisticsService
    ) {
        $this->topScoreRepository = $topScoreRepository;
        $this->contestParticipationRepository = $contestParticipationRepository;
        $this->statisticsService = $statisticsService;

        $this->statisticsKey = new StatisticsKey(StatisticsKey::QUIZ_TOT);
    }

    /**
     * @param \Ramsey\Uuid\UuidInterface $companyId
     * @return array|RankedCompanyParticipant[]
     */
    public function getRankedCompanyParticipants(UuidInterface $companyId): array
    {
        $topScores = $this->topScoreRepository->getAllByCompany($companyId);

        $answer1 = $this->getTiebreaker1Answer();
        $answer2 = $this->getTiebreaker2Answer();

        $rankedCompanyParticipants = array_map(
            function (TopScore $topScore) {
                $contestParticipation = $this->contestParticipationRepository->getByYearAndEmailAndChannel(
                    new Year(2021),
                    $topScore->getEmail(),
                    new QuizChannel(QuizChannel::INDIVIDUAL)
                );

                return new RankedCompanyParticipant(
                    $topScore,
                    $contestParticipation ? $contestParticipation->getAnswer1() : null,
                    $contestParticipation ? $contestParticipation->getAnswer2() : null
                );
            },
            $topScores
        );

        usort(
            $rankedCompanyParticipants,
            function (RankedCompanyParticipant $p1, RankedCompanyParticipant $p2) use ($answer1, $answer2) {
                return $p1->compare($p2, $answer1, $answer2);
            }
        );

        return array_reverse($rankedCompanyParticipants, false);
    }

    /**
     * @param \Ramsey\Uuid\UuidInterface $companyId
     * @return array|RankedCompanyParticipant[]
     */
    public function getTopTenOfPassedCompanyParticipants(UuidInterface $companyId): array
    {
        $rankedParticipants = $this->getRankedCompanyParticipants($companyId);

        $topTen = [];

        for ($i = 0; $i < 10 && $i < count($rankedParticipants); $i++) {
            if ($rankedParticipants[$i]->getScore()->toNative() >= 7) {
                $topTen[] = $rankedParticipants[$i];
            }
        }

        return $topTen;
    }

    public function getTiebreaker1Answer(): NaturalNumber
    {
        return new NaturalNumber(
            $this->statisticsService->getFinishedQuizCounts()[$this->statisticsKey->toNative()]
        );
    }

    public function getTiebreaker2Answer(): NaturalNumber
    {
        return new NaturalNumber(
            $this->statisticsService->getPassedQuizCounts()[$this->statisticsKey->toNative()]
        );
    }
}
