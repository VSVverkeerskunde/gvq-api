<?php declare(strict_types=1);

namespace VSV\GVQ_API\Dashboard\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DashboardViewController extends AbstractController
{
    /**
     * @return Response
     */
    public function dashboard(): Response
    {
        return $this->render(
            'dashboard/dashboard.html.twig'
        );
    }
}
