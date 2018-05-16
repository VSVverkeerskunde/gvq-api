<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Controllers;

use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\Question\Repositories\CategoryRepository;
use VSV\GVQ_API\Question\Serializers\CategoriesSerializer;

class CategoryController
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var CategoriesSerializer
     */
    private $categoriesSerializer;

    /**
     * @param CategoryRepository $categoryRepository
     * @param CategoriesSerializer $categoriesSerializer
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        CategoriesSerializer $categoriesSerializer
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->categoriesSerializer = $categoriesSerializer;
    }

    /**
     * @return Response
     */
    public function getAll(): Response
    {
        $categories = $this->categoryRepository->getAll();

        if ($categories === null) {
            $response = new Response('[]');
        } else {
            $json = $this->categoriesSerializer->serialize($categories, 'json');
            $response = new Response($json);
        }

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
