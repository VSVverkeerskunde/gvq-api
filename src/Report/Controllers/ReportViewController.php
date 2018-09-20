<?php declare(strict_types=1);

namespace VSV\GVQ_API\Report\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Statistics\Service\StatisticsService;

class ReportViewController extends AbstractController
{
    /**
     * @var StatisticsService
     */
    private $statisticsService;

    /**
     * @param StatisticsService $statisticsService
     */
    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    /**
     * @return Response
     */
    public function report(): Response
    {
        $correctNlQuestions = $this->statisticsService->getCorrectQuestions(
            new Language(Language::NL)
        )->toArray();

        $inCorrectNlQuestions = $this->statisticsService->getInCorrectQuestions(
            new Language(Language::NL)
        )->toArray();

        $correctFrQuestions = $this->statisticsService->getCorrectQuestions(
            new Language(Language::FR)
        )->toArray();

        $inCorrectFrQuestions = $this->statisticsService->getInCorrectQuestions(
            new Language(Language::FR)
        )->toArray();

        return $this->render(
            'report/report.html.twig',
            [
                'correctNlQuestions' => $correctNlQuestions,
                'inCorrectNlQuestions' => $inCorrectNlQuestions,
                'correctFrQuestions' => $correctFrQuestions,
                'inCorrectFrQuestions' => $inCorrectFrQuestions,
                'uploadPath' => getenv('UPLOAD_PATH'),
            ]
        );
    }
}
