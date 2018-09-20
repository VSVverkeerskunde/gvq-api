<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Statistics\Service\StatisticsService;

class StatisticsViewController extends AbstractController
{
    /**
     * @var Year
     */
    private $year;

    /**
     * @var StatisticsService
     */
    private $statisticsService;

    /**
     * @param Year $year
     * @param StatisticsService $statisticsService
     */
    public function __construct(
        Year $year,
        StatisticsService $statisticsService
    ) {
        $this->year = $year;
        $this->statisticsService = $statisticsService;
    }

    /**
     * @return Response
     */
    public function statistics(): Response
    {
        $startedCounts = $this->statisticsService->getStartedQuizCounts();
        $finishedCounts = $this->statisticsService->getFinishedQuizCounts();
        $uniqueParticipantCounts = $this->statisticsService->getUniqueParticipantCounts();
        $passedUniqueParticipantCounts = $this->statisticsService->getPassedUniqueParticipantCounts();
        $passedUniqueParticipantPercentage = $this->statisticsService->getPassedUniqueParticipantPercentages();
        $detailedTopScoreAverages = $this->statisticsService->getDetailedTopScoreAverages();
        $partnersCounts = $this->statisticsService->getUniqueParticipantCountsForPartnersByYear($this->year);
        $correctNlQuestions = $this->statisticsService->getCorrectQuestions(new Language(Language::NL))->toArray();
        $inCorrectNlQuestions = $this->statisticsService->getInCorrectQuestions(new Language(Language::NL))->toArray();
        $correctFrQuestions = $this->statisticsService->getCorrectQuestions(new Language(Language::FR))->toArray();
        $inCorrectFrQuestions = $this->statisticsService->getInCorrectQuestions(new Language(Language::FR))->toArray();

        return $this->render(
            'statistics/statistics.html.twig',
            [
                'startedCounts' => $startedCounts,
                'finishedCounts' => $finishedCounts,
                'uniqueParticipantCounts' => $uniqueParticipantCounts,
                'passedUniqueParticipantCounts' => $passedUniqueParticipantCounts,
                'passedUniqueParticipantPercentage' => $passedUniqueParticipantPercentage,
                'detailedTopScoreAverages' => $detailedTopScoreAverages,
                'partnersCounts' => $partnersCounts,
                'correctNlQuestions' => $correctNlQuestions,
                'inCorrectNlQuestions' => $inCorrectNlQuestions,
                'correctFrQuestions' => $correctFrQuestions,
                'inCorrectFrQuestions' => $inCorrectFrQuestions,
            ]
        );
    }
}
