<?php declare(strict_types=1);

namespace VSV\GVQ_API\Document\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @param Request $request
     * @return Response
     */
    public function documents(Request $request): Response
    {
        $language = $request->getLocale();
        $finder = new Finder();
        $finder->files()->in($this->documentsPath.$language);

        return $this->render(
            'documents/documents.html.twig',
            [
                'files' => $finder,
            ]
        );
    }
}
