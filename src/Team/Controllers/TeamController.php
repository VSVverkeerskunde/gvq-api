<?php declare(strict_types=1);

namespace VSV\GVQ_API\Team\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Statistics\Serializers\TeamScoresNormalizer;
use VSV\GVQ_API\Team\Service\TeamService;

class TeamController extends AbstractController
{
    /**
     * @var TeamService
     */
    private $teamService;

    /**
     * @var TeamScoresNormalizer
     */
    private $teamScoresNormalizer;

    /**
     * @var Year
     */
    private $year;

    /**
     * @var array
     */
    private $teamsAsYml;

    /**
     * @param TeamService $teamService
     * @param TeamScoresNormalizer $teamScoresNormalizer
     * @param Year $year
     * @param string $teamFile
     */
    public function __construct(
        TeamService $teamService,
        TeamScoresNormalizer $teamScoresNormalizer,
        Year $year,
        string $teamFile
    ) {
        $this->teamService = $teamService;
        $this->teamScoresNormalizer = $teamScoresNormalizer;
        $this->year = $year;

        $this->teamsAsYml = Yaml::parseFile($teamFile);
    }

    /**
     * @return Response
     */
    public function teamRanking(): Response
    {
        $teamScores = $this->teamService->getRankedTeamScores();

        if ($teamScores === null) {
            return new JsonResponse();
        }

        $teamScoresAsJson = $this->teamScoresNormalizer->normalize($teamScores);

        return new JsonResponse($teamScoresAsJson);
    }

    /**
     * @return Response
     */
    public function teams(): Response
    {
        if (!key_exists($this->year->toNative(), $this->teamsAsYml)) {
            return new JsonResponse();
        }

        return new JsonResponse($this->teamsAsYml[$this->year->toNative()]);
    }
}
