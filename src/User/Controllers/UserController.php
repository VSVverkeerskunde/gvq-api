<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\User\Models\User;
use VSV\GVQ_API\User\Repositories\UserRepository;

class UserController
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
     * @param UserRepository $userRepository
     * @param SerializerInterface $userSerializer
     */
    public function __construct(
        UserRepository $userRepository,
        SerializerInterface $userSerializer
    ) {
        $this->userRepository = $userRepository;
        $this->userSerializer = $userSerializer;
    }

    // TODO: This method is not needed, can be refactored to update which is needed.
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
}
