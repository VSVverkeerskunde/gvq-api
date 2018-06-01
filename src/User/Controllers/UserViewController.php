<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class UserViewController extends AbstractController
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return $this->render(
            'users/index.html.twig'
        );
    }
}
