<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Controllers;

use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\Languages;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Image\Controllers\ImageController;
use VSV\GVQ_API\Question\Forms\ImageFormType;
use VSV\GVQ_API\Question\Forms\QuestionFormType;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Models\Questions;
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
     * @var ImageFormType
     */
    private $imageFormType;

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
        $this->imageFormType = new ImageFormType();
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
                'questions' => $questions ? $questions->sortByNewest()->toArray() : [],
            ]
        );
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function print(Request $request): Response
    {
        $questions = $this->questionRepository->getAll();

        if ($questions) {
            $questions = $questions->sortByNewest();

            $languageFilter = $this->getLanguageFilter($request);

            if ($languageFilter) {
                $questionsArray = $this->filterQuestions($questions, $languageFilter);
            } else {
                $questionsArray = $questions->toArray();
            }

            return $this->render(
                'questions/print.html.twig',
                [
                    'questions' => $questionsArray,
                ]
            );
        }

        $this->addFlash('warning', $this->translator->trans('Questions.print.none'));

        return $this->redirectToRoute('questions_view_index');
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \League\Flysystem\FileExistsException
     * @throws \Exception
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

            $this->addFlash(
                'success',
                $this->translator->trans(
                    'Question.add.success',
                    [
                        '%id%' => $question->getId()->toString(),
                    ]
                )
            );

            return $this->redirectToRoute('questions_view_index');
        }

        return $this->render(
            'questions/add.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param Request $request
     * @param string $id
     * @return Response
     * @throws \Exception
     */
    public function edit(Request $request, string $id): Response
    {
        $question = $this->questionRepository->getById(
            $this->uuidFactory->fromString($id)
        );

        if (!$question) {
            $this->addFlash(
                'warning',
                $this->translator->trans(
                    'Question.edit.not.found',
                    [
                        '%id%' => $id,
                    ]
                )
            );

            return $this->redirectToRoute('questions_view_index');
        }

        $form = $this->createQuestionForm($question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $question = $this->questionFormType->updateQuestionFromData(
                $this->uuidFactory,
                $question,
                $form->getData()
            );
            $this->questionRepository->update($question);

            $this->addFlash(
                'success',
                $this->translator->trans(
                    'Question.edit.success',
                    [
                        '%id%' => $id,
                    ]
                )
            );

            return $this->redirectToRoute('questions_view_index');
        }

        return $this->render(
            'questions/add.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param Request $request
     * @param string $id
     * @return Response
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function editImage(Request $request, string $id): Response
    {
        $question = $this->questionRepository->getById(
            $this->uuidFactory->fromString($id)
        );

        if (!$question) {
            $this->addFlash(
                'warning',
                $this->translator->trans(
                    'Question.edit.not.found',
                    [
                        '%id%' => $id,
                    ]
                )
            );

            return $this->redirectToRoute('questions_view_index');
        }

        $form = $this->createEditImageForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $fileName = $this->imageController->handleImage($data['image']);
            $this->imageController->delete($question->getImageFileName()->toNative());

            $question = $this->updateQuestionImage(
                $question,
                $fileName
            );
            $this->questionRepository->update($question);

            $this->addFlash(
                'success',
                $this->translator->trans(
                    'Question.edit.image.success',
                    [
                        '%id%' => $id,
                    ]
                )
            );

            return $this->redirectToRoute('questions_view_index');
        }

        return $this->render(
            'questions/edit_image.html.twig',
            [
                'form' => $form->createView(),
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

            $this->addFlash(
                'success',
                $this->translator->trans(
                    'Question.delete.success',
                    [
                        '%id%' => $id,
                    ]
                )
            );

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

    /**
     * @return FormInterface
     */
    private function createEditImageForm(): FormInterface
    {
        $formBuilder = $this->createFormBuilder();

        $this->imageFormType->buildForm(
            $formBuilder,
            [
                'translator' => $this->translator,
            ]
        );

        return $formBuilder->getForm();
    }

    /**
     * @param Request $request
     * @return null|Language
     */
    private function getLanguageFilter(Request $request): ?Language
    {
        $printNL = $request->query->get('print_nl') === 'on';
        $printFR = $request->query->get('print_fr') === 'on';

        if ($printNL && !$printFR) {
            return new Language('nl');
        }

        if (!$printNL && $printFR) {
            return new Language('fr');
        }

        return null;
    }

    /**
     * @param Questions $questions
     * @param Language $language
     * @return Question[]
     */
    private function filterQuestions(
        Questions $questions,
        Language $language
    ): array {
        $filteredQuestions = [];

        foreach ($questions as $question) {
            if ($question->getLanguage()->equals($language)) {
                $filteredQuestions[] = $question;
            }
        }

        return $filteredQuestions;
    }

    /**
     * @param Question $question
     * @param NotEmptyString $imageFileName
     * @return Question
     */
    public function updateQuestionImage(
        Question $question,
        NotEmptyString $imageFileName
    ): Question {
        return new Question(
            $question->getId(),
            $question->getLanguage(),
            $question->getYear(),
            $question->getCategory(),
            $question->getText(),
            $imageFileName,
            $question->getAnswers(),
            $question->getFeedback(),
            $question->getCreatedOn()
        );
    }
}
