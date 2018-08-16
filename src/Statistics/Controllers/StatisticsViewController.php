<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class StatisticsViewController extends AbstractController
{
    /**
     * @return Response
     */
    public function statistics(): Response
    {
        return $this->render('statistics/statistics.html.twig');
    }
}
