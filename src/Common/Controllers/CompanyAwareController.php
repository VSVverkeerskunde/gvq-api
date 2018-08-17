<?php declare(strict_types=1);

namespace VSV\GVQ_API\Common\Controllers;

use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use VSV\GVQ_API\Company\Models\Companies;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\User\Repositories\UserRepository;
use VSV\GVQ_API\User\ValueObjects\Email;

class CompanyAwareController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * @param UserRepository $userRepository
     * @param CompanyRepository $companyRepository
     */
    public function __construct(
        UserRepository $userRepository,
        CompanyRepository $companyRepository
    ) {
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
    }

    /**
     * @return Companies|null
     */
    protected function getCompaniesForUser(): ?Companies
    {
        $user = $this->userRepository->getByEmail(new Email($this->getUser()->getUsername()));
        if ($user === null) {
            return null;
        }

        if ($this->get('security.authorization_checker')->isGranted(['ROLE_VSV', 'ROLE_ADMIN'])) {
            return $this->companyRepository->getAll();
        }

        if ($this->get('security.authorization_checker')->isGranted(['ROLE_CONTACT'])) {
            return $this->companyRepository->getAllByUser($user);
        }
    }

    /**
     * @param Companies|null $companies
     * @param string|null $companyId
     * @return Company
     */
    protected function getActiveCompany(
        ?Companies $companies,
        ?string $companyId
    ): ?Company {
        if ($companies === null) {
            return null;
        }

        if ($companyId === null) {
            return $companies->getIterator()->current();
        }

        $company = $this->companyRepository->getById(Uuid::fromString($companyId));
        if ($company === null) {
            return null;
        }

        if (!$this->hasAccessRightsOnCompany($companies, $company)) {
            throw new AccessDeniedHttpException();
        }

        return $company;
    }

    /**
     * @param Company $company
     */
    protected function updateCompany(Company $company): void
    {
        $this->companyRepository->update($company);
    }

    /**
     * @param Companies $companies
     * @param Company $activeCompany
     * @return bool
     */
    private function hasAccessRightsOnCompany(
        Companies $companies,
        Company $activeCompany
    ): bool {
        /** @var Company $company */
        foreach ($companies as $company) {
            if ($company->getId()->equals($activeCompany->getId())) {
                return true;
            }
        }

        return false;
    }
}
