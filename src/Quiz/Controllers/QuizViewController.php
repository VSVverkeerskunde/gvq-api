<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\Company\ValueObjects\Alias;
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
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * @param Year $year
     * @param bool $allowAnonymous
     */
    public function __construct(
        Year $year,
        bool $allowAnonymous,
        CompanyRepository $companyRepository
    ) {
        $this->year = $year;
        $this->allowAnonymous = $allowAnonymous;
        $this->companyRepository = $companyRepository;
    }

    /**
     * @param ContainerInterface $container
     * @param Request $request
     * @return Response
     */
    public function showQuiz(ContainerInterface $container, Request $request): Response
    {
        if ($request->query->get('channel') === 'company') {
            $companyAlias = $request->query->get('company');

            $company = $this->companyRepository->getByAlias(new Alias($companyAlias));

            $language = $request->get('language');

            if (!$company) {
                return $this->render(
                    'quiz/company_not_found.html.twig',
                    [
                        'company_alias' => $companyAlias,
                        'language' => $language,
                    ]
                );
            }
        }

        if ($this->canPlayQuiz()) {
            $teams = Yaml::parseFile(
                $container->getParameter('kernel.project_dir') . '/config/teams.yaml'
            );

            $currentYearTeams = $teams[$this->year->toNative()] ?? [];

            return $this->render(
                'quiz/quiz.html.twig',
                [
                    'teams' => (object)$currentYearTeams,
                ]
            );
        } else {
            $language = $request->get('language');
            $channel = $request->get('channel') === 'league' ? 'league' : 'quiz';

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
            $authChecker = $this->get('security.authorization_checker');

            $allowedRoles = ['ROLE_ADMIN', 'ROLE_VSV', 'ROLE_TEST'];

            foreach ($allowedRoles as $allowedRole) {
                if ($authChecker->isGranted($allowedRole)) {
                    return true;
                }
            }

            return false;
        }
    }
}
