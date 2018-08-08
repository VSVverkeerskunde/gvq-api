<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\EventListeners;

use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListener;
use VSV\GVQ_API\Quiz\Events\AbstractAnsweredEvent;
use VSV\GVQ_API\Quiz\Repositories\CurrentQuestionRepository;

class AnsweredEventListener implements EventListener
{
    /**
     * @var CurrentQuestionRepository
     */
    private $currentQuestionRepository;

    /**
     * @param CurrentQuestionRepository $currentQuestionRepository
     */
    public function __construct(CurrentQuestionRepository $currentQuestionRepository)
    {
        $this->currentQuestionRepository = $currentQuestionRepository;
    }

    /**
     * @param DomainMessage $domainMessage
     */
    public function handle(DomainMessage $domainMessage)
    {
        $payload = $domainMessage->getPayload();

        if ($payload instanceof AbstractAnsweredEvent) {
            $this->currentQuestionRepository->save(
                $payload->getId(),
                $payload->getQuestion()
            );
        }
    }
}
