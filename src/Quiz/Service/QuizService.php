<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Service;

use Ramsey\Uuid\UuidFactoryInterface;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
use VSV\GVQ_API\Partner\Models\Partner;
use VSV\GVQ_API\Question\Models\Categories;
use VSV\GVQ_API\Question\Models\Questions;
use VSV\GVQ_API\Question\Repositories\CategoryRepository;
use VSV\GVQ_API\Question\Repositories\QuestionRepository;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Quiz\Models\Quiz;
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
     * @param UuidFactoryInterface $uuidFactory
     * @param Year $year
     * @param AllowedDelay $allowedDelay
     */
    public function __construct(
        QuestionRepository $questionRepository,
        CategoryRepository $categoryRepository,
        UuidFactoryInterface $uuidFactory,
        Year $year,
        AllowedDelay $allowedDelay
    ) {
        $this->questionRepository = $questionRepository;
        $this->categoryRepository = $categoryRepository;
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
     * @param Year $year
     * @return Quiz
     */
    public function generateQuiz(
        QuizParticipant $participant,
        QuizChannel $channel,
        ?Company $company,
        ?Partner $partner,
        ?Team $team,
        Language $language,
        Year $year
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
