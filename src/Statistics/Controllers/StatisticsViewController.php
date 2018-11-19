<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Common\Controllers\ResponseFactory;
use VSV\GVQ_API\Common\CsvResponse;
use VSV\GVQ_API\Company\CompaniesCsvData;
use VSV\GVQ_API\Company\Models\Companies;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Statistics\Service\StatisticsService;
use VSV\GVQ_API\Team\Repositories\TeamRepository;

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
     * @var TeamRepository
     */
    private $teamRepository;

    /**
     * @param Year $year
     * @param StatisticsService $statisticsService
     * @param SerializerInterface $serializer
     * @param ResponseFactory $responseFactory
     * @param TeamRepository $teamRepository
     */
    public function __construct(
        Year $year,
        StatisticsService $statisticsService,
        SerializerInterface $serializer,
        ResponseFactory $responseFactory,
        TeamRepository $teamRepository
    ) {
        $this->year = $year;
        $this->statisticsService = $statisticsService;
        $this->serializer = $serializer;
        $this->responseFactory = $responseFactory;
        $this->teamRepository = $teamRepository;
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
        $teams = $this->teamRepository->getAllByYear($this->year);

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
                'teams' => $teams,
            ]
        );
    }

    /**
     * @return Response
     */
    public function export(): Response
    {
        $companies = $this->statisticsService->getTopCompanies();

        $csvData = new CompaniesCsvData($companies->getIterator(), $this->serializer);

        return new CsvResponse(
            'top_companies.csv',
            $csvData->rows()
        );
    }
}
