<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Common\Serializers\JsonEnricher;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\User\Models\LoginDetails;
use VSV\GVQ_API\User\Repositories\UserRepository;

class AccountController
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
     * @var JsonEnricher
     */
    private $jsonEnricher;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param UserRepository $userRepository
     * @param CompanyRepository $companyRepository
     * @param SerializerInterface $serializer
     * @param JsonEnricher $jsonEnricher
     */
    public function __construct(
        UserRepository $userRepository,
        CompanyRepository $companyRepository,
        SerializerInterface $serializer,
        JsonEnricher $jsonEnricher
    ) {
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
        $this->serializer = $serializer;
        $this->jsonEnricher = $jsonEnricher;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function register(Request $request): Response
    {
        /** @var string $json */
        $json = $request->getContent();
        $json = $this->jsonEnricher->enrich($json);

        /** @var Company $company */
        $company = $this->serializer->deserialize($json, Company::class, 'json');

        $user = $company->getUser();
        $this->userRepository->save($user);

        $this->companyRepository->save($company);

        $response = new Response('{"id":"'.$user->getId()->toString().'"}');
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function login(Request $request): Response
    {
        $json = $request->getContent();
        $loginDetails = new LoginDetails(json_decode($json, true));

        $user = $this->userRepository->getByEmail($loginDetails->getEmail());
        if ($user === null) {
            throw new \InvalidArgumentException('Login failed.');
        }

        if (!$user->getPassword() ||
            !$user->getPassword()->verifies($loginDetails->getPassword())) {
            throw new \InvalidArgumentException('Login failed.');
        }

        $response = new Response(
            $this->serializer->serialize($user, 'json')
        );
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
