<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\User\Repositories\UserRepository;

class UserViewController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        $users = $this->userRepository->getAll();

        return $this->render(
            'users/index.html.twig',
            [
                'users' => $users ? $users->toArray(): [],
            ]
        );
    }
}
