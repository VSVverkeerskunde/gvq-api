<?php declare(strict_types=1);

namespace VSV\GVQ_API\Report\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Report\Service\ReportService;

class ReportViewController extends AbstractController
{
    /**
     * @var ReportService
     */
    private $reportService;

    /**
     * @param ReportService $reportService
     */
    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * @return Response
     */
    public function report(): Response
    {
        $correctNlQuestions = $this->reportService->getCorrectQuestions(
            new Language(Language::NL)
        )->toArray();

        $inCorrectNlQuestions = $this->reportService->getInCorrectQuestions(
            new Language(Language::NL)
        )->toArray();

        $correctFrQuestions = $this->reportService->getCorrectQuestions(
            new Language(Language::FR)
        )->toArray();

        $inCorrectFrQuestions = $this->reportService->getInCorrectQuestions(
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
