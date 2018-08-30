<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\Statistics\Service\StatisticsService;

class StatisticsViewController extends AbstractController
{
    /**
     * @var StatisticsService
     */
    private $statisticsService;

    /**
     * @param StatisticsService $statisticsService
     */
    public function __construct(StatisticsService $statisticsService)
    {
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
        $partnersCounts = $this->statisticsService->getUniqueParticipantCountsForPartners();

        return $this->render(
            'statistics/statistics.html.twig',
            [
                'startedCounts' => $startedCounts,
                'finishedCounts' => $finishedCounts,
                'uniqueParticipantCounts' => $uniqueParticipantCounts,
                'partnersCounts' => $partnersCounts,
            ]
        );
    }
}
