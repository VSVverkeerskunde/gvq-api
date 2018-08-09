<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\EventListeners;

use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListener;
use VSV\GVQ_API\Quiz\Events\AbstractAnsweredEvent;
use VSV\GVQ_API\Quiz\Repositories\CurrentQuestionResultRepository;

class AnsweredEventListener implements EventListener
{
    /**
     * @var CurrentQuestionResultRepository
     */
    private $currentQuestionResultRepository;

    /**
     * @param CurrentQuestionResultRepository $currentQuestionResultRepository
     */
    public function __construct(CurrentQuestionResultRepository $currentQuestionResultRepository)
    {
        $this->currentQuestionResultRepository = $currentQuestionResultRepository;
    }

    /**
     * @param DomainMessage $domainMessage
     */
    public function handle(DomainMessage $domainMessage)
    {
        $payload = $domainMessage->getPayload();

        if ($payload instanceof AbstractAnsweredEvent) {
            $this->currentQuestionResultRepository->save(
                $payload->getId(),
                $payload->getQuestionResult()
            );
        }
    }
}
