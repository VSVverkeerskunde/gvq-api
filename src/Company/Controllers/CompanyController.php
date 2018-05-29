<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;

class CompanyController
{
    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * @var SerializerInterface
     */
    private $companySerializer;

    /**
     * @param CompanyRepository $companyRepository
     * @param SerializerInterface $companySerializer
     */
    public function __construct(
        CompanyRepository $companyRepository,
        SerializerInterface $companySerializer
    ) {
        $this->companyRepository = $companyRepository;
        $this->companySerializer = $companySerializer;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function save(Request $request): Response
    {
        $json = $request->getContent();
        /** @var Company $company */
        $company = $this->companySerializer->deserialize($json, Company::class, 'json');

        $this->companyRepository->save($company);

        $response = new Response('{"id":"'.$company->getId()->toString().'"}');
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
