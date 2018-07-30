<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Projections;

use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListener;
use VSV\GVQ_API\Quiz\Events\QuizStarted;
use VSV\GVQ_API\Quiz\Repositories\StartedQuizRepository;
use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;

class StartedQuizProjection implements EventListener
{
    /**
     * @var StartedQuizRepository
     */
    private $startedQuizzesRepository;

    /**
     * @param StartedQuizRepository $startedQuizzesRepository
     */
    public function __construct(StartedQuizRepository $startedQuizzesRepository)
    {
        $this->startedQuizzesRepository = $startedQuizzesRepository;
    }

    /**
     * @param DomainMessage $domainMessage
     */
    public function handle(DomainMessage $domainMessage)
    {
        $payload = $domainMessage->getPayload();

        if ($payload instanceof QuizStarted) {
            $this->startedQuizzesRepository->incrementTotal(
                StatisticsKey::createFromQuiz($payload->getQuiz())
            );
        }
    }
}
