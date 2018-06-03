<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Controllers;

use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\Common\ValueObjects\Languages;
use VSV\GVQ_API\Question\Forms\QuestionFormDTO;
use VSV\GVQ_API\Question\Forms\QuestionFormType;
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
     * @var UuidFactoryInterface
     */
    private $uuidFactory;

    /**
     * @param QuestionRepository $questionRepository
     * @param CategoryRepository $categoryRepository
     * @param UuidFactoryInterface $uuidFactory
     */
    public function __construct(
        QuestionRepository $questionRepository,
        CategoryRepository $categoryRepository,
        UuidFactoryInterface $uuidFactory
    ) {
        $this->questionRepository = $questionRepository;
        $this->categoryRepository = $categoryRepository;
        $this->uuidFactory = $uuidFactory;
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
        $categories = $this->categoryRepository->getAll();

        $questionFormDTO = new QuestionFormDTO();

        $form = $this->createForm(
            QuestionFormType::class,
            $questionFormDTO,
            [
                'languages' => new Languages(),
                'categories' => $categories,
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $question = $questionFormDTO->toQuestion($this->uuidFactory);
            $this->questionRepository->save($question);
        }

        return $this->render(
            'questions/add.html.twig',
            [
                'categories' => $categories ? $categories->toArray() : [],
                'form' => $form->createView()
            ]
        );
    }
}
