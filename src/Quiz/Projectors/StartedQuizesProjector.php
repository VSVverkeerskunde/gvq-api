<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListener;
use VSV\GVQ_API\Quiz\Events\QuizStarted;
use VSV\GVQ_API\Quiz\Repositories\StartedQuizRepository;
use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;

class StartedQuizesProjector implements EventListener
{
    /**
     * @var StartedQuizRepository
     */
    private $startedQuizzesRepository;

    /**
     * @param StartedQuizRepository $startedQuizRepository
     */
    public function __construct(StartedQuizRepository $startedQuizRepository)
    {
        $this->startedQuizzesRepository = $startedQuizRepository;
    }

    /**
     * @param DomainMessage $domainMessage
     */
    public function handle(DomainMessage $domainMessage)
    {
        $payload = $domainMessage->getPayload();

        if ($payload instanceof QuizStarted) {
            $this->startedQuizzesRepository->incrementCount(
                StatisticsKey::createFromQuiz($payload->getQuiz())
            );
        }
    }
}
