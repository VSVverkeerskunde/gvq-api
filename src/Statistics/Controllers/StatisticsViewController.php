<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
        $partnersCounts = $this->statisticsService->getUniqueParticipantCountsForPartnersByYear($this->year);

        return $this->render(
            'statistics/statistics.html.twig',
            [
                'startedCounts' => $startedCounts,
                'finishedCounts' => $finishedCounts,
                'uniqueParticipantCounts' => $uniqueParticipantCounts,
                'passedUniqueParticipantCounts' => $passedUniqueParticipantCounts,
                'passedUniqueParticipantPercentage' => $passedUniqueParticipantPercentage,
                'partnersCounts' => $partnersCounts,
            ]
        );
    }
}
