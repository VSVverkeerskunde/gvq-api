<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Controllers;

use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\Common\ValueObjects\Languages;
use VSV\GVQ_API\Image\Controllers\ImageController;
use VSV\GVQ_API\Question\Forms\QuestionFormDTO;
use VSV\GVQ_API\Question\Forms\QuestionFormType;
use VSV\GVQ_API\Question\Repositories\CategoryRepository;
use VSV\GVQ_API\Question\Repositories\QuestionRepository;

class QuestionViewController extends AbstractController
{
    /**
     * @var UuidFactoryInterface
     */
    private $uuidFactory;

    /**
     * @var QuestionRepository
     */
    private $questionRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var ImageController
     */
    private $imageController;

    /**
     * @param UuidFactoryInterface $uuidFactory
     * @param QuestionRepository $questionRepository
     * @param CategoryRepository $categoryRepository
     * @param ImageController $imageController
     */
    public function __construct(
        UuidFactoryInterface $uuidFactory,
        QuestionRepository $questionRepository,
        CategoryRepository $categoryRepository,
        ImageController $imageController
    ) {
        $this->uuidFactory = $uuidFactory;
        $this->questionRepository = $questionRepository;
        $this->categoryRepository = $categoryRepository;
        $this->imageController = $imageController;
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
     * @throws \League\Flysystem\FileExistsException
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
            $fileName = $this->imageController->handleImage($questionFormDTO->image);

            $question = $questionFormDTO->toNewQuestion(
                $this->uuidFactory,
                $fileName
            );
            $this->questionRepository->save($question);

            $this->addFlash('success', 'De nieuwe vraag is bewaard.');
        }

        return $this->render(
            'questions/add.html.twig',
            [
                'categories' => $categories ? $categories->toArray() : [],
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @param Request $request
     * @param string $id
     * @return Response
     */
    public function edit(Request $request, string $id): Response
    {
        $categories = $this->categoryRepository->getAll();

        $question = $this->questionRepository->getById(
            $this->uuidFactory->fromString($id)
        );

        $questionFormDTO = new QuestionFormDTO();
        $questionFormDTO->fromQuestion($question);

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
            $question = $questionFormDTO->toExistingQuestion($question);
            $this->questionRepository->update($question);

            $this->addFlash('success', 'De vraag is aangepast.');
        }

        return $this->render(
            'questions/add.html.twig',
            [
                'categories' => $categories ? $categories->toArray() : [],
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @param Request $request
     * @param string $id
     * @return Response
     */
    public function delete(Request $request, string $id): Response
    {
        if ($request->getMethod() === 'POST') {
            $this->questionRepository->delete(
               $this->uuidFactory->fromString($id)
            );

            $this->addFlash('success', 'De vraag is verwijderd.');

            return $this->redirectToRoute('questions_view_index');
        }

        return $this->render(
            'questions/delete.html.twig',
            [
                'id' => $id,
            ]
        );
    }
}
