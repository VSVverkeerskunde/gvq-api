<?php declare(strict_types=1);

namespace VSV\GVQ_API\Dashboard\Controllers;

use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\Common\Controllers\CompanyAwareController;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\Dashboard\Service\DashboardService;
use VSV\GVQ_API\User\Repositories\UserRepository;

class DashboardViewController extends CompanyAwareController
{
    /**
     * @var DashboardService
     */
    private $dashboardService;

    /**
     * @param UserRepository $userRepository
     * @param CompanyRepository $companyRepository
     * @param DashboardService $dashboardService
     */
    public function __construct(
        UserRepository $userRepository,
        CompanyRepository $companyRepository,
        DashboardService $dashboardService
    ) {
        parent::__construct($userRepository, $companyRepository);

        $this->dashboardService = $dashboardService;
    }

    /**
     * @param string $companyId
     * @return Response
     */
    public function dashboard(?string $companyId): Response
    {
        $companies = $this->getCompaniesForUser();

        $company = $this->getActiveCompany($companies, $companyId);

        if ($company === null) {
            throw new \InvalidArgumentException('Found no active company!');
        }

        $employeeParticipationRatio = $this->dashboardService->getEmployeeParticipationRatio(
            $company->getId()
        );

        $companyAverage = $this->dashboardService->getAverageEmployeeTopScore(
            $company->getId()
        );

        $average = $this->dashboardService->getAverageTopScore();

        $topScores = $this->dashboardService->getTopScoresByCompany(
            $company->getId()
        )->toArray();

        return $this->render(
            'dashboard/dashboard.html.twig',
            [
                'companies' => $companies? $companies->toArray() : [],
                'company' => $company,
                'employeeParticipationRatio' => $employeeParticipationRatio,
                'companyAverage' => $companyAverage,
                'average' => $average,
                'topScores' => $topScores
            ]
        );
    }
}
