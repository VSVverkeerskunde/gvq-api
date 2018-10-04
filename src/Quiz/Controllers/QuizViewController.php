<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Team\Repositories\TeamRepository;
use VSV\GVQ_API\Team\Serializers\TeamsNormalizer;

class QuizViewController extends AbstractController
{
    /**
     * @var Year
     */
    private $year;

    /**
     * @var TeamRepository
     */
    private $teamRepository;

    /**
     * @var TeamsNormalizer
     */
    private $teamsNormalizer;

    /**
     * @param Year $year
     * @param TeamRepository $teamRepository
     * @param TeamsNormalizer $teamsNormalizer
     */
    public function __construct(Year $year, TeamRepository $teamRepository, TeamsNormalizer $teamsNormalizer)
    {
        $this->year = $year;
        $this->teamRepository = $teamRepository;
        $this->teamsNormalizer = $teamsNormalizer;
    }

    /**
     * @return Response
     */
    public function showQuiz(): Response
    {
        $teams = $this->teamRepository->getAllByYear($this->year);

        $teamsAsJson = $teams ? $this->teamsNormalizer->normalize($teams) : null;

        return $this->render(
            'quiz/quiz.html.twig',
            [
                'teams' => $teamsAsJson,
            ]
        );
    }
}
