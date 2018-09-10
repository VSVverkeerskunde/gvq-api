<?php declare(strict_types=1);

namespace VSV\GVQ_API\Dashboard\Service;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\Statistics\Repositories\EmployeeParticipationRepository;
use VSV\GVQ_API\Statistics\Repositories\TopScoreRepository;
use VSV\GVQ_API\Statistics\ValueObjects\Average;
use VSV\GVQ_API\Statistics\ValueObjects\EmployeeParticipationRatio;

class DashboardService
{
    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * @var EmployeeParticipationRepository
     */
    private $employeeParticipationRepository;

    /**
     * @var TopScoreRepository
     */
    private $topScoreRepository;

    /**
     * @param CompanyRepository $companyRepository
     * @param EmployeeParticipationRepository $employeeParticipationRepository
     * @param TopScoreRepository $topScoreRepository
     */
    public function __construct(
        CompanyRepository $companyRepository,
        EmployeeParticipationRepository $employeeParticipationRepository,
        TopScoreRepository $topScoreRepository
    ) {
        $this->companyRepository = $companyRepository;
        $this->employeeParticipationRepository = $employeeParticipationRepository;
        $this->topScoreRepository = $topScoreRepository;
    }

    /**
     * @param UuidInterface $companyId
     * @return EmployeeParticipationRatio
     * @throws \InvalidArgumentException
     */
    public function getEmployeeParticipationRatio(UuidInterface $companyId): EmployeeParticipationRatio
    {
        $company = $this->getCompany($companyId);

        return new EmployeeParticipationRatio(
            $this->employeeParticipationRepository->countByCompany($companyId),
            $company->getNumberOfEmployees()
        );
    }

    /**
     * @param UuidInterface $companyId
     * @return Average
     */
    public function getAverageEmployeeTopScore(UuidInterface $companyId): Average
    {
        $company = $this->getCompany($companyId);

        return $this->topScoreRepository->getAverageForCompany(
            $company->getId()
        );
    }

    /**
     * @param UuidInterface $companyId
     * @return Company
     */
    private function getCompany(UuidInterface $companyId): Company
    {
        $company = $this->companyRepository->getById($companyId);

        if (null === $company) {
            throw new \InvalidArgumentException('Unknown company');
        }

        return $company;
    }
}
