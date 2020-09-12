<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Common\Controllers\ResponseFactory;
use VSV\GVQ_API\Common\CsvResponse;
use VSV\GVQ_API\Company\CompaniesCsvData;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Statistics\Repositories\CompanyPlayedQuizzesRepository;
use VSV\GVQ_API\Statistics\Repositories\EmployeeParticipationRepository;
use VSV\GVQ_API\Statistics\Repositories\TeamParticipantRepository;
use VSV\GVQ_API\Statistics\Repositories\TopScoreRepository;
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
     * @var CompanyPlayedQuizzesRepository
     */
    private $companyPlayedQuizzesRepository;

    /**
     * @var EmployeeParticipationRepository
     */
    private $employeeParticipationRepository;

    /**
     * @var TopScoreRepository
     */
    private $topScoreRepository;

    /**
     * @var TeamParticipantRepository
     */
    private $teamParticipantRepository;

    /**
     * @param Year $year
     * @param StatisticsService $statisticsService
     * @param SerializerInterface $serializer
     * @param ResponseFactory $responseFactory
     * @param TeamRepository $teamRepository
     * @param CompanyPlayedQuizzesRepository $companyPlayedQuizzesRepository
     * @param \VSV\GVQ_API\Statistics\Repositories\EmployeeParticipationRepository $employeeParticipationRepository
     * @param \VSV\GVQ_API\Statistics\Repositories\TopScoreRepository $topScoreRepository
     * @param TeamParticipantRepository $teamParticipantRepository
     */
    public function __construct(
        Year $year,
        StatisticsService $statisticsService,
        SerializerInterface $serializer,
        ResponseFactory $responseFactory,
        TeamRepository $teamRepository,
        CompanyPlayedQuizzesRepository $companyPlayedQuizzesRepository,
        EmployeeParticipationRepository $employeeParticipationRepository,
        TopScoreRepository $topScoreRepository,
        TeamParticipantRepository $teamParticipantRepository
    ) {
        $this->year = $year;
        $this->statisticsService = $statisticsService;
        $this->serializer = $serializer;
        $this->responseFactory = $responseFactory;
        $this->teamRepository = $teamRepository;
        $this->companyPlayedQuizzesRepository = $companyPlayedQuizzesRepository;
        $this->employeeParticipationRepository = $employeeParticipationRepository;
        $this->topScoreRepository = $topScoreRepository;
        $this->teamParticipantRepository = $teamParticipantRepository;
    }

    /**
     * @return Response
     */
    public function statistics(): Response
    {
        $startedCounts = $this->statisticsService->getStartedQuizCounts();
        $finishedCounts = $this->statisticsService->getFinishedQuizCounts();
        $passedCounts = $this->statisticsService->getPassedQuizCounts();
        $uniqueParticipantCounts = $this->statisticsService->getUniqueParticipantCounts();
        $passedUniqueParticipantCounts = $this->statisticsService->getPassedUniqueParticipantCounts();
        $passedUniqueParticipantPercentage = $this->statisticsService->getPassedUniqueParticipantPercentages();
        $detailedTopScoreAverages = $this->statisticsService->getDetailedTopScoreAverages();
        $partnersCounts = $this->statisticsService->getUniqueParticipantCountsForPartnersByYear($this->year);
        $teams = $this->teamRepository->getAllByYear($this->year);

        $teamParticipants = [];
        /** @var \VSV\GVQ_API\Team\Models\Team[] $teams */
        foreach ($teams as $team) {
            $teamParticipants[$team->getId()->toString()] =
                $this->teamParticipantRepository->getParticipantCount(
                    $team->getId()
                );
        }

        return $this->render(
            'statistics/statistics.html.twig',
            [
                'startedCounts' => $startedCounts,
                'finishedCounts' => $finishedCounts,
                'passedCounts' => $passedCounts,
                'uniqueParticipantCounts' => $uniqueParticipantCounts,
                'passedUniqueParticipantCounts' => $passedUniqueParticipantCounts,
                'passedUniqueParticipantPercentage' => $passedUniqueParticipantPercentage,
                'detailedTopScoreAverages' => $detailedTopScoreAverages,
                'partnersCounts' => $partnersCounts,
                'teams' => $teams,
                'teamParticipants' => $teamParticipants,
            ]
        );
    }

    /**
     * @return Response
     */
    public function export(): Response
    {
        $companies = $this->statisticsService->getTopCompanies();

        $csvData = new CompaniesCsvData(
            $companies,
            $this->companyPlayedQuizzesRepository,
            $this->employeeParticipationRepository,
            $this->topScoreRepository,
            $this->serializer
        );

        return new CsvResponse(
            'top_companies.csv',
            $csvData->rows()
        );
    }
}
