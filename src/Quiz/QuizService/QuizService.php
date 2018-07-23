<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\QuizService;

use Ramsey\Uuid\UuidFactoryInterface;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
use VSV\GVQ_API\Question\Models\Categories;
use VSV\GVQ_API\Question\Models\Questions;
use VSV\GVQ_API\Question\Repositories\CategoryRepository;
use VSV\GVQ_API\Question\Repositories\QuestionRepository;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Quiz\Models\Quiz;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
use VSV\GVQ_API\Quiz\ValueObjects\QuizParticipant;
use VSV\GVQ_API\Quiz\ValueObjects\QuizType;

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
     * @var UuidFactoryInterface
     */
    private $uuidFactory;

    /**
     * @var array
     */
    private $distributionKey = [
        'a7910bf1-05f9-4bdb-8dee-1256cbfafc0b' => 2,
        '15530c78-2b1c-4820-bcfb-e04c5e2224b9' => 3,
        '67844067-82ca-4698-a713-b5fbd4c729c5' => 2,
        '58ee6bd3-a3f4-42bc-ba81-82491cec55b9' => 1,
        '1289d4b5-e88e-4b3c-9223-eb2c7c49f4d0' => 1,
        '9677995d-5fc5-48cd-a251-565b626cb7c1' => 1,
        'fce11f3c-24ad-4aed-b00d-0069e3404749' => 1,
        '6f0c9e04-1e84-4ba4-be54-ab5747111754' => 4,
    ];

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
     * @param QuizParticipant $participant
     * @param QuizType $type
     * @param QuizChannel $channel
     * @param Language $language
     * @param Year $year
     * @return Quiz
     * @throws \Exception
     */
    public function generateQuiz(
        QuizParticipant $participant,
        QuizType $type,
        QuizChannel $channel,
        Language $language,
        Year $year
    ): Quiz {
        $quiz = new Quiz(
            $this->uuidFactory->uuid4(),
            $participant,
            $type,
            $channel,
            $language,
            $year,
            $this->generateQuestions($language, $year)
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
        $questionsArray = [];

        /** @var Categories $categories */
        $categories = $this->categoryRepository->getAll();

        foreach ($categories as $category) {
            $pickedQuestions = $this->questionRepository->getSubset(
                $language,
                $category,
                $year,
                new PositiveNumber($this->distributionKey[$category->getId()->toString()])
            );

            if ($pickedQuestions) {
                $questionsArray = array_merge($questionsArray, $pickedQuestions->toArray());
            }
        }

        shuffle($questionsArray);

        return new Questions(...$questionsArray);
    }
}
