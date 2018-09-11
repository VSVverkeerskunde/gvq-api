<?php declare(strict_types=1);

namespace VSV\GVQ_API\Team\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\Team\Service\TeamService;

class TeamController extends AbstractController
{
    /**
     * @var TeamService
     */
    private $teamService;

    /**
     * @param TeamService $teamService
     */
    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    public function teamRanking(): Response
    {
        $response = new Response();
        $response->setContent(
            $this->teamService->getTeamRankingAsJson()
        );
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
