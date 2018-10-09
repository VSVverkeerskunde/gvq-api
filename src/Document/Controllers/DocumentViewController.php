<?php declare(strict_types=1);

namespace VSV\GVQ_API\Document\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\Common\ValueObjects\Language;

class DocumentViewController extends AbstractController
{
    /**
     * @var string
     */
    private $documentsPath;

    /**
     * @param string $documentsPath
     */
    public function __construct(string $documentsPath)
    {
        $this->documentsPath = $documentsPath;
    }

    /**
     * @return Response
     */
    public function documents(): Response
    {
        $finder = new Finder();
        $dutchFiles = $finder->files()->in($this->documentsPath.Language::NL);

        $finder = new Finder();
        $frenchFiles = $finder->files()->in($this->documentsPath.Language::FR);

        return $this->render(
            'documents/documents.html.twig',
            [
                'dutchFiles' => $dutchFiles,
                'frenchFiles' => $frenchFiles,
            ]
        );
    }
}
