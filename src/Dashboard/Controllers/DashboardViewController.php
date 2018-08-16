<?php declare(strict_types=1);

namespace VSV\GVQ_API\Dashboard\Controllers;

use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use VSV\GVQ_API\Company\Models\Companies;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\User\Repositories\UserRepository;
use VSV\GVQ_API\User\ValueObjects\Email;

class DashboardViewController extends AbstractController
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
     * @param string $companyId
     * @return Response
     */
    public function dashboard(?string $companyId): Response
    {
        $companies = $this->getCompaniesForUser();

        $company = $this->getActiveCompany($companies, $companyId);

        return $this->render(
            'dashboard/dashboard.html.twig',
            [
                'companies' => $companies? $companies->toArray() : [],
                'company' => $company,
            ]
        );
    }

    /**
     * @return null|Companies
     */
    private function getCompaniesForUser(): ?Companies
    {
        $companies = null;

        if ($this->get('security.authorization_checker')->isGranted(['ROLE_VSV', 'ROLE_ADMIN'])) {
            $companies = $this->companyRepository->getAll();
        } elseif ($this->get('security.authorization_checker')->isGranted(['ROLE_CONTACT'])) {
            $user = $this->userRepository->getByEmail(new Email($this->getUser()->getUsername()));
            if ($user !== null) {
                $companies = $this->companyRepository->getAllByUser($user);
            }
        }

        return $companies;
    }

    /**
     * @param Companies $companies
     * @param null|string $companyId
     * @return Company
     */
    private function getActiveCompany(
        Companies $companies,
        ?string $companyId
    ): Company {
        if (!empty($companyId)) {
            $company = $this->companyRepository->getById(Uuid::fromString($companyId));

            if (!$this->hasAccessRightsOnCompany($companies, $company)) {
                throw new AccessDeniedHttpException();
            }
        } else {
            $company = $companies->getIterator()->current();
        }

        return $company;
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
