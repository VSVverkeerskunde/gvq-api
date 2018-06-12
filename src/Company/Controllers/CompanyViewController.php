<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;

class CompanyViewController extends AbstractController
{
    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * @param CompanyRepository $companyRepository
     */
    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        $companies = $this->companyRepository->getAll();

        return $this->render(
            'companies/index.html.twig',
            [
                'companies' => $companies ? $companies->toArray(): [],
            ]
        );
    }
}
