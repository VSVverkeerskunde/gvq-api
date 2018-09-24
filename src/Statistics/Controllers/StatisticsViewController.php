<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Common\Controllers\ResponseFactory;
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
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * @param Year $year
     * @param StatisticsService $statisticsService
     * @param SerializerInterface $serializer
     * @param ResponseFactory $responseFactory
     */
    public function __construct(
        Year $year,
        StatisticsService $statisticsService,
        SerializerInterface $serializer,
        ResponseFactory $responseFactory
    ) {
        $this->year = $year;
        $this->statisticsService = $statisticsService;
        $this->serializer = $serializer;
        $this->responseFactory = $responseFactory;
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
            ]
        );
    }

    /**
     * @return Response
     */
    public function export(): Response
    {
        $companies = $this->statisticsService->getTopCompanies();

        if ($companies) {
            $companiesAsCsv = $this->serializer->serialize(
                $companies,
                'csv'
            );
        } else {
            $companiesAsCsv = '';
        }

        $response = $this->responseFactory->createCsvResponse(
            $companiesAsCsv,
            'top_companies'
        );

        return $response;
    }
}
