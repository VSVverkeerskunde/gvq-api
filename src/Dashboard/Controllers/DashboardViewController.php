<?php declare(strict_types=1);

namespace VSV\GVQ_API\Dashboard\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Common\Controllers\CompanyAwareController;
use VSV\GVQ_API\Common\Controllers\ResponseFactory;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\Dashboard\Service\DashboardService;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Statistics\Repositories\CompanyQuestionDifficultyRepositoryFactory;
use VSV\GVQ_API\Statistics\Service\CompanyParticipantRanker;
use VSV\GVQ_API\Statistics\Service\StatisticsService;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;
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
     * @var CompanyParticipantRanker
     */
    private $companyParticipantRanker;

    /**
     * @var CompanyQuestionDifficultyRepositoryFactory
     */
    private $questionDifficultyRepositoryFactory;

    /**
     * @var bool
     */
    private $allowContact;

    /**
     * @param Year $year
     * @param UserRepository $userRepository
     * @param CompanyRepository $companyRepository
     * @param DashboardService $dashboardService
     * @param StatisticsService $statisticsService
     * @param SerializerInterface $serializer
     * @param ResponseFactory $responseFactory
     * @param CompanyParticipantRanker $companyParticipantRanker
     * @param CompanyQuestionDifficultyRepositoryFactory $questionDifficultyRepositoryFactory
     * @param bool $allowContact
     */
    public function __construct(
        Year $year,
        UserRepository $userRepository,
        CompanyRepository $companyRepository,
        DashboardService $dashboardService,
        StatisticsService $statisticsService,
        SerializerInterface $serializer,
        ResponseFactory $responseFactory,
        CompanyParticipantRanker $companyParticipantRanker,
        CompanyQuestionDifficultyRepositoryFactory $questionDifficultyRepositoryFactory,
        bool $allowContact
    ) {
        parent::__construct($userRepository, $companyRepository);

        $this->year = $year;
        $this->dashboardService = $dashboardService;
        $this->statisticsService = $statisticsService;
        $this->serializer = $serializer;
        $this->responseFactory = $responseFactory;
        $this->companyParticipantRanker = $companyParticipantRanker;
        $this->questionDifficultyRepositoryFactory = $questionDifficultyRepositoryFactory;
        $this->allowContact = $allowContact;
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

        $topTen = $this->companyParticipantRanker->getTopTenOfPassedCompanyParticipants($activeCompany->getId());

        $uniqueParticipantCounts = $this->statisticsService->getUniqueParticipantCounts();
        $passedUniqueParticipantCounts = $this->statisticsService->getPassedUniqueParticipantCounts();
        $passedUniqueParticipantPercentage = $this->statisticsService->getPassedUniqueParticipantPercentages();
        $detailedTopScoreAverages = $this->statisticsService->getDetailedTopScoreAverages();

        $tiebreaker1Answer = 0;
        $tiebreaker2Answer = 0;
        if ($this->allowContact) {
            $tiebreaker1Answer = $this->companyParticipantRanker->getTiebreaker1Answer();
            $tiebreaker2Answer = $this->companyParticipantRanker->getTiebreaker2Answer();
        }

        $questionDifficultyRepository = $this->questionDifficultyRepositoryFactory->forCompany($activeCompany->getId());
        $range = new NaturalNumber(4);
        $nl = new Language('nl');
        $fr = new Language('fr');

        $correctNlQuestions = $questionDifficultyRepository->getBestRange($nl, $range)->toArray();
        $inCorrectNlQuestions = $questionDifficultyRepository->getWorstRange($nl, $range)->toArray();
        $correctFrQuestions = $questionDifficultyRepository->getBestRange($fr, $range)->toArray();
        $inCorrectFrQuestions = $questionDifficultyRepository->getWorstRange($fr, $range)->toArray();

        $companyUniqueParticipantCounts = $this->dashboardService->uniqueParticipants($activeCompany->getId());
        $companyPassedUniqueParticipantCounts = $this->dashboardService->uniquePassedParticipants($activeCompany->getId());
        $companyPassedUniqueParticipantPercentage = $this->dashboardService->uniquePassedParticipantsPercentages($activeCompany->getId());
        $companyDetailedTopScoreAverages = $this->dashboardService->averageTopscores($activeCompany->getId());


        return $this->render(
            'dashboard/dashboard.html.twig',
            [
                'companies' => $companies? $companies->toArray() : [],
                'activeCompany' => $activeCompany,
                'employeeParticipationRatio' => $employeeParticipationRatio,
                'companyAverage' => $companyAverage,
                'average' => $average,
                'topScores' => $topTen,
                'uniqueParticipantCounts' => $uniqueParticipantCounts,
                'passedUniqueParticipantCounts' => $passedUniqueParticipantCounts,
                'passedUniqueParticipantPercentage' => $passedUniqueParticipantPercentage,
                'detailedTopScoreAverages' => $detailedTopScoreAverages,
                'showTiebreakerAnswers' => $this->allowContact,
                'tiebreaker1Answer' => $tiebreaker1Answer,
                'tiebreaker2Answer' => $tiebreaker2Answer,
                'correctNlQuestions' => $correctNlQuestions,
                'inCorrectNlQuestions' => $inCorrectNlQuestions,
                'correctFrQuestions' => $correctFrQuestions,
                'inCorrectFrQuestions' => $inCorrectFrQuestions,
                'uploadPath' => getenv('UPLOAD_PATH'),
                'companyUniqueParticipantCounts' => $companyUniqueParticipantCounts,
                'companyPassedUniqueParticipantCounts' => $companyPassedUniqueParticipantCounts,
                'companyPassedUniqueParticipantPercentage' => $companyPassedUniqueParticipantPercentage,
                'companyDetailedTopScoreAverages' => $companyDetailedTopScoreAverages,
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

        $rankedParticipants = $this->companyParticipantRanker->getRankedCompanyParticipants($activeCompany->getId());
        $topScoresAsCsv = $this->serializer->serialize($rankedParticipants, 'csv');

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
