<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListener;
use VSV\GVQ_API\Quiz\Events\AbstractAnsweredEvent;
use VSV\GVQ_API\Quiz\Events\AnsweredCorrect;
use VSV\GVQ_API\Quiz\Events\AnsweredIncorrect;
use VSV\GVQ_API\Quiz\Events\AnsweredTooLate;
use VSV\GVQ_API\Quiz\Events\QuestionAsked;
use VSV\GVQ_API\Quiz\Events\QuizFinished;
use VSV\GVQ_API\Quiz\Repositories\QuestionResultRepository;
use VSV\GVQ_API\Quiz\ValueObjects\QuestionResult;

class QuestionResultProjector implements EventListener
{
    /**
     * @var QuestionResultRepository
     */
    private $questionResultRepository;

    /**
     * @param QuestionResultRepository $questionResultRepository
     */
    public function __construct(QuestionResultRepository $questionResultRepository)
    {
        $this->questionResultRepository = $questionResultRepository;
    }

    /**
     * @param DomainMessage $domainMessage
     */
    public function handle(DomainMessage $domainMessage)
    {
        $payload = $domainMessage->getPayload();

        if ($payload instanceof QuestionAsked) {
            $this->questionResultRepository->save(
                $payload->getId(),
                new QuestionResult(
                    $payload->getQuestion(),
                    null,
                    null
                ),
                [
                    'questionAsked' => true,
                ]
            );
        } elseif ($payload instanceof AbstractAnsweredEvent) {
            $this->questionResultRepository->save(
                $payload->getId(),
                new QuestionResult(
                    $payload->getQuestion(),
                    null,
                    null
                )
            );
        } elseif ($payload instanceof AnsweredTooLate) {
            $this->questionResultRepository->save(
                $payload->getId(),
                new QuestionResult(
                    $payload->getQuestion(),
                    true,
                    null
                )
            );
        } elseif ($payload instanceof QuizFinished) {
            $questionResult = $this->questionResultRepository->getById(
                $payload->getId()
            );
            $this->questionResultRepository->save(
                $payload->getId(),
                // Extra event gets triggered on last question.
                // This QuizFinished event is used to update the score
                // of the last question result.
                new QuestionResult(
                    $questionResult->getQuestion(),
                    $questionResult->isAnsweredTooLate(),
                    $payload->getScore()
                )
            );
        }
    }
}
