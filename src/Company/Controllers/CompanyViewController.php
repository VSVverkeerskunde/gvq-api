<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class CompanyViewController extends AbstractController
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return $this->render(
            'companies/index.html.twig'
        );
    }
}
