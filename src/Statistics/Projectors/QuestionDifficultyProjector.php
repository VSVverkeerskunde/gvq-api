<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListener;
use VSV\GVQ_API\Quiz\Events\AnsweredCorrect;
use VSV\GVQ_API\Quiz\Events\AnsweredIncorrect;
use VSV\GVQ_API\Quiz\Events\AnsweredTooLate;
use VSV\GVQ_API\Statistics\Repositories\QuestionDifficultyRepository;

class QuestionDifficultyProjector implements EventListener
{
    /** @var QuestionDifficultyRepository */
    private $questionCorrectRepository;

    /** @var QuestionDifficultyRepository */
    private $questionInCorrectRepository;

    /** @var QuestionDifficultyRepository */
    private $questionTooLateRepository;

    /**
     * @param QuestionDifficultyRepository $questionCorrectRepository
     * @param QuestionDifficultyRepository $questionInCorrectRepository
     * @param QuestionDifficultyRepository $questionTooLateRepository
     */
    public function __construct(
        QuestionDifficultyRepository $questionCorrectRepository,
        QuestionDifficultyRepository $questionInCorrectRepository,
        QuestionDifficultyRepository $questionTooLateRepository
    ) {
        $this->questionCorrectRepository = $questionCorrectRepository;
        $this->questionInCorrectRepository = $questionInCorrectRepository;
        $this->questionTooLateRepository = $questionTooLateRepository;
    }

    /**
     * @inheritdoc
     */
    public function handle(DomainMessage $domainMessage)
    {
        $payload = $domainMessage->getPayload();

        if ($payload instanceof AnsweredCorrect) {
            $this->questionCorrectRepository->increment($payload->getQuestion());
        } elseif ($payload instanceof AnsweredIncorrect) {
            $this->questionInCorrectRepository->increment($payload->getQuestion());
        } elseif ($payload instanceof AnsweredTooLate) {
            $this->questionTooLateRepository->increment($payload->getQuestion());
        }
    }
}
