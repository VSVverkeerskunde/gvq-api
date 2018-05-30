<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Common\Serializers\JsonEnricher;
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
    private $serializer;

    /**
     * @var JsonEnricher
     */
    private $jsonEnricher;

    /**
     * @param CompanyRepository $companyRepository
     * @param SerializerInterface $companySerializer
     * @param JsonEnricher $jsonEnricher
     */
    public function __construct(
        CompanyRepository $companyRepository,
        SerializerInterface $companySerializer,
        JsonEnricher $jsonEnricher
    ) {
        $this->companyRepository = $companyRepository;
        $this->serializer = $companySerializer;
        $this->jsonEnricher = $jsonEnricher;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function save(Request $request): Response
    {
        /** @var string $json */
        $json = $request->getContent();
        $json = $this->jsonEnricher->enrich($json);

        /** @var Company $company */
        $company = $this->serializer->deserialize($json, Company::class, 'json');

        $this->companyRepository->save($company);

        $response = new Response('{"id":"'.$company->getId()->toString().'"}');
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
