<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\EventListeners;

use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListener;
use VSV\GVQ_API\Quiz\Events\QuizStarted;
use VSV\GVQ_API\Quiz\Repositories\QuizRepository;
use VSV\GVQ_API\Quiz\Repositories\StartedQuizRepository;
use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;

class QuizStartedListener implements EventListener
{
    /**
     * @var QuizRepository
     */
    private $quizRepository;

    /**
     * @var StartedQuizRepository
     */
    private $startedQuizzesRepository;

    /**
     * @param QuizRepository $quizRepository
     * @param StartedQuizRepository $startedQuizRepository
     */
    public function __construct(
        QuizRepository $quizRepository,
        StartedQuizRepository $startedQuizRepository
    ) {
        $this->quizRepository = $quizRepository;
        $this->startedQuizzesRepository = $startedQuizRepository;
    }

    /**
     * @param DomainMessage $domainMessage
     */
    public function handle(DomainMessage $domainMessage)
    {
        $payload = $domainMessage->getPayload();

        if ($payload instanceof QuizStarted) {
            $this->quizRepository->save($payload->getQuiz());

            $this->startedQuizzesRepository->incrementCount(
                StatisticsKey::createFromQuiz($payload->getQuiz())
            );
        }
    }
}
