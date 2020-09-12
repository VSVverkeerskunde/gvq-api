<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListener;
use VSV\GVQ_API\Quiz\Events\QuizFinished;
use VSV\GVQ_API\Statistics\Repositories\FinishedQuizRepository;
use VSV\GVQ_API\Quiz\Repositories\QuizRepository;
use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;
use VSV\GVQ_API\Statistics\Repositories\PassedQuizRepository;

class FinishedQuizzesProjector implements EventListener
{
    /**
     * @var FinishedQuizRepository
     */
    private $finishedQuizzesRepository;

    /**
     * @var QuizRepository
     */
    private $quizRepository;

    /**
     * @var PassedQuizRepository
     */
    private $passedQuizRepository;

    /**
     * @param FinishedQuizRepository $finishedQuizRepository
     * @param QuizRepository $quizRepository
     */
    public function __construct(
        FinishedQuizRepository $finishedQuizRepository,
        PassedQuizRepository $passedQuizRepository,
        QuizRepository $quizRepository
    ) {
        $this->finishedQuizzesRepository = $finishedQuizRepository;
        $this->passedQuizRepository = $passedQuizRepository;
        $this->quizRepository = $quizRepository;
    }

    /**
     * @param DomainMessage $domainMessage
     */
    public function handle(DomainMessage $domainMessage)
    {
        $payload = $domainMessage->getPayload();

        if ($payload instanceof QuizFinished) {
            $quiz = $this->quizRepository->getById($payload->getId());

            $this->finishedQuizzesRepository->incrementCount(
                StatisticsKey::createFromQuiz($quiz)
            );

            if ($payload->getScore() >= 7) {
                $this->passedQuizRepository->incrementCount(
                    StatisticsKey::createFromQuiz($quiz)
                );
            }
        }
    }
}
