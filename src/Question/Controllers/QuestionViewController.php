<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Controllers;

use function GuzzleHttp\Promise\all;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\Repositories\CategoryRepository;
use VSV\GVQ_API\Question\Repositories\QuestionRepository;

class QuestionViewController extends AbstractController
{
    /**
     * @var QuestionRepository
     */
    private $questionRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @param QuestionRepository $questionRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        QuestionRepository $questionRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->questionRepository = $questionRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        $questions = $this->questionRepository->getAll();

        return $this->render(
            'questions/index.html.twig',
            [
                'questions' => $questions ? $questions->toArray() : [],
            ]
        );
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response
    {
        var_dump($request->request->all());
        var_dump($request->files);

        $categories = $this->categoryRepository->getAll();
        return $this->render(
            'questions/add.html.twig',
            [
                'categories' => $categories ? $categories->toArray() : [],
            ]
        );
    }
}
