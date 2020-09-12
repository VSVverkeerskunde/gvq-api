<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Service;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactoryInterface;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Partner\Models\Partner;
use VSV\GVQ_API\Question\Models\Categories;
use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Question\Models\Questions;
use VSV\GVQ_API\Question\Repositories\CategoryRepository;
use VSV\GVQ_API\Question\Repositories\QuestionRepository;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Quiz\Models\Quiz;
use VSV\GVQ_API\Quiz\Repositories\QuizCompositionRepository;
use VSV\GVQ_API\Quiz\ValueObjects\AllowedDelay;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
use VSV\GVQ_API\Quiz\ValueObjects\QuizParticipant;
use VSV\GVQ_API\Team\Models\Team;

class QuizService
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
     * @var QuizCompositionRepository
     */
    private $quizCompositionRepository;

    /**
     * @var UuidFactoryInterface
     */
    private $uuidFactory;

    /**
     * @var Year
     */
    private $year;

    /**
     * @var AllowedDelay
     */
    private $allowedDelay;

    /**
     * @param QuestionRepository $questionRepository
     * @param CategoryRepository $categoryRepository
     * @param QuizCompositionRepository $quizCompositionRepository
     * @param UuidFactoryInterface $uuidFactory
     * @param Year $year
     * @param AllowedDelay $allowedDelay
     */
    public function __construct(
        QuestionRepository $questionRepository,
        CategoryRepository $categoryRepository,
        QuizCompositionRepository $quizCompositionRepository,
        UuidFactoryInterface $uuidFactory,
        Year $year,
        AllowedDelay $allowedDelay
    ) {
        $this->questionRepository = $questionRepository;
        $this->categoryRepository = $categoryRepository;
        $this->quizCompositionRepository = $quizCompositionRepository;
        $this->uuidFactory = $uuidFactory;
        $this->year = $year;
        $this->allowedDelay = $allowedDelay;
    }

    /**
     * @param QuizParticipant $participant
     * @param QuizChannel $channel
     * @param null|Company $company
     * @param null|Partner $partner
     * @param null|Team $team
     * @param Language $language
     * @param string|null $firstQuestionId
     * @return Quiz
     * @throws \Exception
     */
    public function generateQuiz(
        QuizChannel $channel,
        ?Company $company,
        ?Partner $partner,
        ?Team $team,
        Language $language,
        string $firstQuestionId = null
    ): Quiz {
        $questions = $this->generateQuestions($language, $this->year, $firstQuestionId);

        $quiz = new Quiz(
            $this->uuidFactory->uuid4(),
            $channel,
            $company,
            $partner,
            $team,
            $language,
            $this->year,
            $this->allowedDelay,
            $questions
        );

        return $quiz;
    }

    /**
     * @param Language $language
     * @param Year $year
     * @param string|null $firstQuestionId
     * @return Questions
     */
    private function generateQuestions(Language $language, Year $year, ?string $firstQuestionId): Questions
    {
        $pickedQuestions = [];
        $firstQuestion = null;

        if ($firstQuestionId) {
            $firstQuestion = $this->questionRepository->getById(Uuid::fromString($firstQuestionId));

            // Ignore first question passed if it doesn't meet the conditions.
            if ($firstQuestion and ($firstQuestion->isArchived() || !$firstQuestion->getYear()->equals($year) || !$firstQuestion->getLanguage()->equals($language))) {
                $firstQuestion = null;
            }
        }

        /** @var Categories|Category[] $categories */
        $categories = $this->categoryRepository->getAll();

        foreach ($categories as $category) {
            $questionCount = $this->quizCompositionRepository->getCountByYearAndCategory($year, $category);

            if ($questionCount !== null) {
                $questions = $this->questionRepository->getByYearAndLanguageAndCategoryAndNotArchived(
                    $year,
                    $language,
                    $category
                );

                if ($questions !== null) {
                    if ($firstQuestion && $firstQuestion->getCategory()->getId()->equals($category->getId())) {
                        $questionCount--;
                        $questions = $questions->without($firstQuestion);
                    }

                    $questionPool = $questions->toArray();

                    shuffle($questionPool);

                    $pickedQuestions = array_merge(
                        $pickedQuestions,
                        array_slice($questionPool, 0, $questionCount)
                    );
                }
            }
        }

        shuffle($pickedQuestions);

        if ($firstQuestion) {
            array_unshift($pickedQuestions, $firstQuestion);
        }

        return new Questions(...$pickedQuestions);
    }

    /**
     * @return Year
     */
    public function getYear(): Year
    {
        return $this->year;
    }

    /**
     * @return AllowedDelay
     */
    public function getAllowedDelay(): AllowedDelay
    {
        return $this->allowedDelay;
    }
}
