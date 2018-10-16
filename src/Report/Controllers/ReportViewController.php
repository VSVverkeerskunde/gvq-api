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
     * @var bool
     */
    private $allowContact;

    /**
     * @param ReportService $reportService
     * @param bool $allowContact
     */
    public function __construct(
        ReportService $reportService,
        bool $allowContact
    ) {
        $this->reportService = $reportService;
        $this->allowContact = $allowContact;
    }

    /**
     * @return Response
     */
    public function report(): Response
    {
        if ($this->canViewReport()) {
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

            $categoriesPercentagesNl = $this->reportService->getCategoriesPercentages(
                new Language(Language::NL)
            );
            $categoriesPercentagesFr = $this->reportService->getCategoriesPercentages(
                new Language(Language::FR)
            );

            return $this->render(
                'report/report.html.twig',
                [
                    'correctNlQuestions' => $correctNlQuestions,
                    'inCorrectNlQuestions' => $inCorrectNlQuestions,
                    'correctFrQuestions' => $correctFrQuestions,
                    'inCorrectFrQuestions' => $inCorrectFrQuestions,
                    'categoriesPercentagesNl' => $categoriesPercentagesNl,
                    'categoriesPercentagesFr' => $categoriesPercentagesFr,
                    'uploadPath' => getenv('UPLOAD_PATH'),
                ]
            );
        } else {
            return $this->redirectToRoute('dashboard');
        }
    }

    /**
     * @return bool
     */
    private function canViewReport(): bool
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_CONTACT')) {
            return $this->allowContact;
        } else {
            // Security on the route is still in place.
            // So this point is admin or vsv role.
            return true;
        }
    }
}
