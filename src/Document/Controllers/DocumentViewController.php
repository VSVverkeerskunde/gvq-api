<?php declare(strict_types=1);

namespace VSV\GVQ_API\Document\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Document\Service\DocumentRepository;

class DocumentViewController extends AbstractController
{
    /**
     * @var DocumentRepository
     */
    private $documentRepository;

    /**
     * @param string $documentsPath
     */
    public function __construct(DocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    /**
     * @return Response
     */
    public function documents(): Response
    {
        $files = $this->documentRepository->getFiles();

        return $this->render(
            'documents/documents.html.twig',
            [
                'dutchFiles' => $files['nl'],
                'frenchFiles' => $files['fr'],
            ]
        );
    }
}
