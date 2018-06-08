<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Controllers;

use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use VSV\GVQ_API\Common\ValueObjects\Languages;
use VSV\GVQ_API\Image\Controllers\ImageController;
use VSV\GVQ_API\Question\Forms\QuestionFormType;
use VSV\GVQ_API\Question\Models\Question;
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
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var QuestionFormType
     */
    private $questionFormType;

    /**
     * @param UuidFactoryInterface $uuidFactory
     * @param QuestionRepository $questionRepository
     * @param CategoryRepository $categoryRepository
     * @param ImageController $imageController
     * @param TranslatorInterface $translator
     */
    public function __construct(
        UuidFactoryInterface $uuidFactory,
        QuestionRepository $questionRepository,
        CategoryRepository $categoryRepository,
        ImageController $imageController,
        TranslatorInterface $translator
    ) {
        $this->uuidFactory = $uuidFactory;
        $this->questionRepository = $questionRepository;
        $this->categoryRepository = $categoryRepository;
        $this->imageController = $imageController;
        $this->translator = $translator;

        $this->questionFormType = new QuestionFormType();
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
     * @return Response
     */
    public function print(): Response
    {
        // TODO: Filter for NL/FR
        $questions = $this->questionRepository->getAll();

        return $this->render(
            'questions/print.html.twig',
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
        $form = $this->createQuestionForm(null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $fileName = $this->imageController->handleImage($data['image']);

            $question = $this->questionFormType->newQuestionFromData(
                $this->uuidFactory,
                $fileName,
                $data
            );
            $this->questionRepository->save($question);

            $this->addFlash('success', 'De nieuwe vraag '.$question->getId()->toString().' is bewaard.');
            return $this->redirectToRoute('questions_view_index');
        }

        return $this->render(
            'questions/add.html.twig',
            [
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
        $question = $this->questionRepository->getById(
            $this->uuidFactory->fromString($id)
        );

        if (!$question) {
            $this->addFlash('warning', 'Geen vraag gevonden met id '.$id.' om aan te passen.');
            return $this->redirectToRoute('questions_view_index');
        }

        $form = $this->createQuestionForm($question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $question = $this->questionFormType->updateQuestionFromData(
                $question,
                $form->getData()
            );
            $this->questionRepository->update($question);

            $this->addFlash('success', 'Vraag '.$id.' is aangepast.');
            return $this->redirectToRoute('questions_view_index');
        }

        return $this->render(
            'questions/add.html.twig',
            [
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

            $this->addFlash('success', 'Vraag '.$id.' is verwijderd.');

            return $this->redirectToRoute('questions_view_index');
        }

        return $this->render(
            'questions/delete.html.twig',
            [
                'id' => $id,
            ]
        );
    }

    /**
     * @param null|Question $question
     * @return FormInterface
     */
    private function createQuestionForm(?Question $question): FormInterface
    {
        $formBuilder = $this->createFormBuilder();

        $this->questionFormType->buildForm(
            $formBuilder,
            [
                'languages' => new Languages(),
                'categories' => $this->categoryRepository->getAll(),
                'question' => $question,
                'translator' => $this->translator,
            ]
        );

        return $formBuilder->getForm();
    }
}
