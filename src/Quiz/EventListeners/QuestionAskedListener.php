<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\EventListeners;

use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListener;
use VSV\GVQ_API\Quiz\Events\QuestionAsked;
use VSV\GVQ_API\Quiz\Repositories\CurrentQuestionRepository;
use VSV\GVQ_API\Quiz\Repositories\CurrentQuestionResultRepository;

class QuestionAskedListener implements EventListener
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

        if ($payload instanceof QuestionAsked) {
            $this->currentQuestionResultRepository->save(
                $payload->getId(),
                $payload->getQuestionResult(),
                [
                    'questionAsked' => true,
                ]
            );
        }
    }
}
