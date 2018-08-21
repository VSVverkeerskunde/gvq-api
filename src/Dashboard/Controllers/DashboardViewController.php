<?php declare(strict_types=1);

namespace VSV\GVQ_API\Dashboard\Controllers;

use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\Common\Controllers\CompanyAwareController;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\User\Repositories\UserRepository;

class DashboardViewController extends CompanyAwareController
{
    /**
     * @param UserRepository $userRepository
     * @param CompanyRepository $companyRepository
     */
    public function __construct(
        UserRepository $userRepository,
        CompanyRepository $companyRepository
    ) {
        parent::__construct($userRepository, $companyRepository);
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
}
