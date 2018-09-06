<?php declare(strict_types=1);

namespace VSV\GVQ_API\Dashboard\Service;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\Statistics\Repositories\EmployeeParticipationRepository;
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
     * @param CompanyRepository $companyRepository
     * @param EmployeeParticipationRepository $employeeParticipationRepository
     */
    public function __construct(
        CompanyRepository $companyRepository,
        EmployeeParticipationRepository $employeeParticipationRepository
    ) {
        $this->companyRepository = $companyRepository;
        $this->employeeParticipationRepository = $employeeParticipationRepository;
    }

    /**
     * @param UuidInterface $companyId
     * @return EmployeeParticipationRatio
     * @throws \InvalidArgumentException
     */
    public function getEmployeeParticipationRatio(UuidInterface $companyId): EmployeeParticipationRatio
    {
        $company = $this->companyRepository->getById($companyId);

        if (null === $company) {
            throw new \InvalidArgumentException('Unknown company');
        }

        return new EmployeeParticipationRatio(
            $this->employeeParticipationRepository->countByCompany($companyId),
            $company->getNumberOfEmployees()
        );
    }

    /**
     * @param UuidInterface $companyId
     * @return int
     */
    public function getAverageEmployeeTopScore(UuidInterface $companyId): int
    {
        $company = $this->companyRepository->getById($companyId);

        if (null === $company) {
            throw new \InvalidArgumentException('Unknown company');
        }
    }
}
