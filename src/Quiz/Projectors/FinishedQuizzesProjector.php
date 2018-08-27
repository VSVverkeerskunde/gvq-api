<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListener;
use VSV\GVQ_API\Quiz\Events\QuizFinished;
use VSV\GVQ_API\Quiz\Repositories\FinishedQuizRepository;
use VSV\GVQ_API\Quiz\Repositories\QuizRepository;
use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;

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
     * @param FinishedQuizRepository $finishedQuizRepository
     * @param QuizRepository $quizRepository
     */
    public function __construct(
        FinishedQuizRepository $finishedQuizRepository,
        QuizRepository $quizRepository
    ) {
        $this->finishedQuizzesRepository = $finishedQuizRepository;
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
        }
    }
}
