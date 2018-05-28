<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\Company\Serializers\CompanySerializer;

class CompanyController
{
    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * @var CompanySerializer
     */
    private $companySerlializer;

    /**
     * @param CompanyRepository $companyRepository
     * @param CompanySerializer $companySerlializer
     */
    public function __construct(CompanyRepository $companyRepository, CompanySerializer $companySerlializer)
    {
        $this->companyRepository = $companyRepository;
        $this->companySerlializer = $companySerlializer;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function save(Request $request): Response
    {
        $json = $request->getContent();
        /** @var Company $company */
        $company = $this->companySerlializer->deserialize($json, Company::class, 'json');
        $this->companyRepository->save($company);

        $response = new Response('{"id":"'.$company->getId()->toString().'"}');
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
