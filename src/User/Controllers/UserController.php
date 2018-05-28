<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\User\Models\LoginDetails;
use VSV\GVQ_API\User\Models\User;
use VSV\GVQ_API\User\Repositories\UserRepository;
use VSV\GVQ_API\User\Serializers\UserSerializer;

class UserController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserSerializer
     */
    private $userSerializer;

    /**
     * @param UserRepository $userRepository
     * @param UserSerializer $userSerializer
     */
    public function __construct(UserRepository $userRepository, UserSerializer $userSerializer)
    {
        $this->userRepository = $userRepository;
        $this->userSerializer = $userSerializer;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function save(Request $request): Response
    {
        $json = $request->getContent();
        /** @var User $user */
        $user = $this->userSerializer->deserialize($json, User::class, 'json');
        $this->userRepository->save($user);

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
            throw new \InvalidArgumentException('No user found');
        }
        if ($user->getPassword()->verifies($loginDetails->getPassword())) {
            $response =  new Response(
                $this->userSerializer->serialize($user, 'json')
            );
        } else {
            $response = new Response('{"id":"null"}');
        }
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
