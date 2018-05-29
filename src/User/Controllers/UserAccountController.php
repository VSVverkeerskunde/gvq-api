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
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param UserRepository $userRepository
     * @param CompanyRepository $companyRepository
     * @param SerializerInterface $serializer
     */
    public function __construct(
        UserRepository $userRepository,
        CompanyRepository $companyRepository,
        SerializerInterface $serializer
    ) {
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
        $this->serializer = $serializer;
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

    /**
     * @param Request $request
     * @return Response
     */
    public function register(Request $request): Response
    {
        $json = $request->getContent();
        /** @var Company $company */
        $company = $this->serializer->deserialize(
            $json,
            Company::class,
            'json',
            [
                'role' => 'contact'
            ]
        );

        $user = $company->getUser();
        $this->userRepository->save($user);

        $this->companyRepository->save($company);

        $response = new Response('{"id":"'.$user->getId()->toString().'"}');
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
