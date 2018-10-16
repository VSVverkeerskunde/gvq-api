<?php declare(strict_types=1);

namespace VSV\GVQ_API\Dashboard\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Common\Controllers\CompanyAwareController;
use VSV\GVQ_API\Common\Controllers\ResponseFactory;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\Dashboard\Service\DashboardService;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Statistics\Models\TopScore;
use VSV\GVQ_API\Statistics\Service\StatisticsService;
use VSV\GVQ_API\User\Repositories\UserRepository;

class DashboardViewController extends CompanyAwareController
{
    /**
     * @var Year
     */
    private $year;

    /**
     * @var DashboardService
     */
    private $dashboardService;

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
     * @param UserRepository $userRepository
     * @param CompanyRepository $companyRepository
     * @param DashboardService $dashboardService
     * @param StatisticsService $statisticsService
     * @param SerializerInterface $serializer
     * @param ResponseFactory $responseFactory
     */
    public function __construct(
        Year $year,
        UserRepository $userRepository,
        CompanyRepository $companyRepository,
        DashboardService $dashboardService,
        StatisticsService $statisticsService,
        SerializerInterface $serializer,
        ResponseFactory $responseFactory
    ) {
        parent::__construct($userRepository, $companyRepository);

        $this->year = $year;
        $this->dashboardService = $dashboardService;
        $this->statisticsService = $statisticsService;
        $this->serializer = $serializer;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @param string $companyId
     * @return Response
     */
    public function dashboard(?string $companyId): Response
    {
        $activeCompany = $this->getCompany($companyId);
        $companies = $this->getCompaniesForUser();

        $employeeParticipationRatio = $this->dashboardService->getEmployeeParticipationRatio(
            $activeCompany->getId()
        );

        $companyAverage = $this->dashboardService->getAverageEmployeeTopScore(
            $activeCompany->getId()
        );

        $average = $this->dashboardService->getAverageTopScore();

        /** @var TopScore[] $allTopScores */
        $allTopScores = $this->dashboardService->getTopScoresByCompany(
            $activeCompany->getId()
        )->toArray();

        $firstTenTopScores = [];
        for ($i = 0; $i < 10 && $i < count($allTopScores); $i++) {
            if ($allTopScores[$i]->getScore()->toNative() >= 11) {
                $firstTenTopScores[] = $allTopScores[$i];
            }
        }

        $passedUniqueParticipantCounts = $this->statisticsService->getPassedUniqueParticipantCounts();
        $passedUniqueParticipantPercentage = $this->statisticsService->getPassedUniqueParticipantPercentages();
        $detailedTopScoreAverages = $this->statisticsService->getDetailedTopScoreAverages();

        return $this->render(
            'dashboard/dashboard.html.twig',
            [
                'companies' => $companies? $companies->toArray() : [],
                'activeCompany' => $activeCompany,
                'employeeParticipationRatio' => $employeeParticipationRatio,
                'companyAverage' => $companyAverage,
                'average' => $average,
                'topScores' => $firstTenTopScores,
                'showTopScoresCard' => count($allTopScores) > 0,
                'passedUniqueParticipantCounts' => $passedUniqueParticipantCounts,
                'passedUniqueParticipantPercentage' => $passedUniqueParticipantPercentage,
                'detailedTopScoreAverages' => $detailedTopScoreAverages,
            ]
        );
    }

    /**
     * @param string $companyId
     * @return Response
     */
    public function export(string $companyId): Response
    {
        $activeCompany = $this->getCompany($companyId);

        $topScores = $this->dashboardService->getTopScoresByCompany($activeCompany->getId());
        $topScoresAsCsv = $this->serializer->serialize($topScores, 'csv');

        $response = $this->responseFactory->createCsvResponse(
            $topScoresAsCsv,
            'topScores'
        );

        return $response;
    }

    /**
     * @param string $companyId
     * @return Company
     */
    private function getCompany(?string $companyId): Company
    {
        $companies = $this->getCompaniesForUser();
        $activeCompany = $this->getActiveCompany($companies, $companyId);

        if ($activeCompany === null) {
            throw new \InvalidArgumentException('Found no active company!');
        }

        return $activeCompany;
    }
}
