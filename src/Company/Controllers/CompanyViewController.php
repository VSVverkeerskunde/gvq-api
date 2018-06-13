<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Common\Controllers\ResponseFactory;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;

class CompanyViewController extends AbstractController
{
    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * @param CompanyRepository $companyRepository
     * @param SerializerInterface $serializer
     * @param ResponseFactory $responseFactory
     */
    public function __construct(
        CompanyRepository $companyRepository,
        SerializerInterface $serializer,
        ResponseFactory $responseFactory
    ) {
        $this->companyRepository = $companyRepository;
        $this->serializer = $serializer;
        $this->responseFactory = $responseFactory;
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

    /**
     * @return Response
     */
    public function export(): Response
    {
        $companies = $this->companyRepository->getAll();
        $companiesAsCsv = $this->serializer->serialize($companies, 'csv');

        $response = $this->responseFactory->createCsvResponse(
            $companiesAsCsv,
            'companies'
        );

        return $response;
    }
}
