<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
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
     * @var bool
     */
    private $allowAnonymous;

    /**
     * @param Year $year
     * @param bool $allowAnonymous
     */
    public function __construct(
        Year $year,
        bool $allowAnonymous
    ) {
        $this->year = $year;
        $this->allowAnonymous = $allowAnonymous;
    }

    /**
     * @param ContainerInterface $container
     * @param Request $request
     * @return Response
     */
    public function showQuiz(ContainerInterface $container, Request $request): Response
    {
        if ($this->canPlayQuiz()) {
            $teams = Yaml::parseFile(
                $container->getParameter('kernel.project_dir') . '/config/teams.yaml'
            );

            return $this->render(
                'quiz/quiz.html.twig',
                [
                    'teams' => (object)$teams[$this->year->toNative()],
                ]
            );
        } else {
            $language = $request->get('language');
            $channel = $request->get('channel') === 'cup' ? 'cup' : 'quiz';

            return $this->render(
                'quiz/quiz-placeholder.html.twig',
                [
                    'language' => $language,
                    'channel' => $channel,
                ]
            );
        }
    }

    /**
     * @return bool
     */
    private function canPlayQuiz(): bool
    {
        if ($this->allowAnonymous) {
            return true;
        } else {
            return $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') ||
                $this->get('security.authorization_checker')->isGranted('ROLE_VSV');
        }
    }
}
