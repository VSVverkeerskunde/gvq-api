<?php declare(strict_types=1);

namespace VSV\GVQ_API\Report\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ReportViewController extends AbstractController
{
    /**
     * @return Response
     */
    public function report(): Response
    {
        return $this->render('report/report.html.twig');
    }
}
