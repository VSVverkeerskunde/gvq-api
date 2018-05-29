<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\User\Models\LoginDetails;
use VSV\GVQ_API\User\Repositories\UserRepository;

class UserAccountController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var SerializerInterface
     */
    private $userSerializer;

    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * @var SerializerInterface
     */
    private $companySerializer;

    /**
     * @param UserRepository $userRepository
     * @param SerializerInterface $userSerializer
     * @param CompanyRepository $companyRepository
     * @param SerializerInterface $companySerializer
     */
    public function __construct(
        UserRepository $userRepository,
        SerializerInterface $userSerializer,
        CompanyRepository $companyRepository,
        SerializerInterface $companySerializer
    ) {
        $this->userSerializer = $userSerializer;
        $this->userRepository = $userRepository;
        $this->companySerializer = $companySerializer;
        $this->companyRepository = $companyRepository;
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
            throw new \InvalidArgumentException('No user found');
        }

        if ($user->getPassword()->verifies($loginDetails->getPassword())) {
            $response = new Response(
                $this->userSerializer->serialize($user, 'json')
            );
        } else {
            $response = new Response('{"id":"null"}');
        }
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function register(Request $request): Response
    {
        $json = $request->getContent();
        /** @var Company $company */
        $company = $this->companySerializer->deserialize($json, Company::class, 'json');

        $user = $company->getUser();
        $this->userRepository->save($user);

        $this->companyRepository->save($company);

        $response = new Response('{"id":"'.$user->getId()->toString().'"}');
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
