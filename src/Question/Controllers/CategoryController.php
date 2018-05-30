<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Question\Repositories\CategoryRepository;

class CategoryController
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param CategoryRepository $categoryRepository
     * @param SerializerInterface $serializer
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        SerializerInterface $serializer
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->serializer = $serializer;
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
            $json = $this->serializer->serialize($categories, 'json');
            $response = new Response($json);
        }

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
