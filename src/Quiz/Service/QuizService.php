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
        QuizParticipant $participant,
        QuizChannel $channel,
        ?Company $company,
        ?Partner $partner,
        ?Team $team,
        Language $language,
        string $firstQuestionId = null
    ): Quiz {
        $questions = $this->getFixedQuestionsForTestParticipant($participant, $language);

        if (count($questions) === 0) {
            $questions = $this->generateQuestions($language, $this->year, $firstQuestionId);
        }

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
            $questions
        );

        return $quiz;
    }

    private function getFixedQuestionsForTestParticipant(QuizParticipant $participant, Language $language) {
        $questionIds = [];

        $emailPartForFixedQuestions = substr($participant->getEmail()->toNative(), -50);

        if ($emailPartForFixedQuestions !== false) {
            switch ($emailPartForFixedQuestions) {
                case '0348ef09-501a-4350-b463-71875cad98e0@2dotstwice.be':
                    $questionIds = [
                        'c9d80098-05f7-4e40-8c1c-cbafcde49c9f',
                        'edf61d91-62e0-45ba-9515-da8eb5c3719b',
                        '57f69ef0-6231-4624-8b55-f62cc29c9f4c',
                        'c0687c0e-59c9-4485-ac7d-4aac88ffe237',
                        '3f0c1f68-1f06-4c6e-8fe6-d085f574cd08',
                        'eb0122be-c3c7-4380-8dcc-fc642a8242ef',
                        '6ea6f72a-3484-4ee0-86da-e99183ab28a1',
                        'c30dbd5b-96f0-4056-bdc1-2e10f2529e41',
                        '0812ff5d-c283-4d8a-9956-712cb90bacab',
                        '8bf3b547-2a07-4541-8a9a-d8fb8b46c858',
                        '085b725b-ec1d-40d6-8f74-e74cb374e2d2',
                        '7b95bda0-7d1c-4ea5-980a-acc22d03f939',
                        '437c1746-c72f-47f8-8db9-112360266465',
                        'a4bd516c-84e8-4e21-a75e-2e56d78fde8d',
                        'd9f67669-4d2c-463f-9e30-eefc90b03d8a',
                    ];
                    break;
            }
        }

        $pickedQuestions = [];
        foreach ($questionIds as $questionId) {
            $pickedQuestion = $this->questionRepository->getById($this->uuidFactory->fromString($questionId));
            if ($pickedQuestion) {
                $pickedQuestions[] = $pickedQuestion;
            }
        }

        return new Questions(...$pickedQuestions);
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
