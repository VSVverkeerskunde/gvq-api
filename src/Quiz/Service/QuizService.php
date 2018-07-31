<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Service;

use Ramsey\Uuid\UuidFactoryInterface;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Partner\Models\Partner;
use VSV\GVQ_API\Question\Models\Categories;
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
     * @return Quiz
     */
    public function generateQuiz(
        QuizParticipant $participant,
        QuizChannel $channel,
        ?Company $company,
        ?Partner $partner,
        ?Team $team,
        Language $language
    ): Quiz {
        $quiz = new Quiz(
            $this->uuidFactory->uuid4(),
            $participant,
            $channel,
            $company,
            $partner,
            $team,
            $language,
            $this->year,
            $this->allowedDelay,
            $this->generateQuestions($language, $this->year)
        );

        return $quiz;
    }

    /**
     * @param Language $language
     * @param Year $year
     * @return Questions
     */
    private function generateQuestions(Language $language, Year $year): Questions
    {
        $pickedQuestions = [];

        /** @var Categories $categories */
        $categories = $this->categoryRepository->getAll();

        foreach ($categories as $category) {
            $questionCount = $this->quizCompositionRepository->getCountByYearAndCategory($year, $category);

            if ($questionCount !== null) {
                $questions = $this->questionRepository->getByYearLanguageAndCategory(
                    $year,
                    $language,
                    $category
                );

                if ($questions !== null) {
                    $questionPool = $questions->toArray();
                    shuffle($questionPool);
                    $pickedQuestions = array_merge($pickedQuestions, array_slice($questionPool, 0, $questionCount));
                }
            }
        }

        shuffle($pickedQuestions);

        return new Questions(...$pickedQuestions);
    }
}
