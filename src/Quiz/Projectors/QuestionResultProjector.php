<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListener;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Quiz\Events\AnsweredCorrect;
use VSV\GVQ_API\Quiz\Events\AnsweredIncorrect;
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
        } elseif ($payload instanceof AnsweredCorrect) {
            $this->questionResultRepository->save(
                $payload->getId(),
                new QuestionResult(
                    $payload->getQuestion(),
                    null,
                    null
                )
            );
        } elseif ($payload instanceof AnsweredIncorrect) {
            $this->questionResultRepository->save(
                $payload->getId(),
                new QuestionResult(
                    $payload->getQuestion(),
                    $payload->isAnsweredTooLate(),
                    null
                )
            );
        } elseif ($payload instanceof QuizFinished) {
            $questionResult = $this->questionResultRepository->getById(
                $payload->getId()
            );
            $this->questionResultRepository->save(
                $payload->getId(),
                new QuestionResult(
                    $questionResult->getQuestion(),
                    $questionResult->isAnsweredTooLate(),
                    $payload->getScore()
                )
            );
        }
    }
}
