<?php declare(strict_types=1);

namespace VSV\GVQ_API\Document\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DocumentViewController extends AbstractController
{
    /**
     * @return Response
     */
    public function documents(): Response
    {
        return $this->render('documents/documents.html.twig');
    }
}
