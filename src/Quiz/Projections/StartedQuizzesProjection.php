<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Projections;

use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListener;
use VSV\GVQ_API\Quiz\Events\QuizStarted;
use VSV\GVQ_API\Quiz\Repositories\StartedQuizzesRepository;
use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;

class StartedQuizzesProjection implements EventListener
{
    /**
     * @var StartedQuizzesRepository
     */
    private $startedQuizzesRepository;

    /**
     * @param StartedQuizzesRepository $startedQuizzesRepository
     */
    public function __construct(StartedQuizzesRepository $startedQuizzesRepository)
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
