<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;
use VSV\GVQ_API\Question\ValueObjects\Year;

class QuizViewController extends AbstractController
{
    /**
     * @var Year
     */
    private $year;

    /**
     * @param Year $year
     */
    public function __construct(Year $year)
    {
        $this->year = $year;
    }

    /**
     * @param ContainerInterface $container
     * @return Response
     */
    public function showQuiz(ContainerInterface $container): Response
    {
        $teams = Yaml::parseFile(
            $container->getParameter('kernel.project_dir').'/config/teams.yaml'
        );

        return $this->render(
            'quiz/quiz.html.twig',
            [
                'teams' => (object) $teams[$this->year->toNative()],
            ]
        );
    }
}
