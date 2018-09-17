<?php declare(strict_types=1);

namespace VSV\GVQ_API\Document\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DocumentViewController extends AbstractController
{
    /**
     * @param Request $request
     * @param ContainerInterface $container
     * @return Response
     */
    public function documents(Request $request, ContainerInterface $container): Response
    {
        $language = $request->getLocale();
        $finder = new Finder();
        $finder->files()->in($container->getParameter('kernel.project_dir').'/public/documents/'.$language);

        return $this->render(
            'documents/documents.html.twig',
            [
                'files' => $finder,
            ]
        );
    }
}
