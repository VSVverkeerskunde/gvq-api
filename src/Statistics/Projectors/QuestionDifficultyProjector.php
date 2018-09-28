<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListener;
use VSV\GVQ_API\Quiz\Events\AnsweredCorrect;
use VSV\GVQ_API\Quiz\Events\AnsweredIncorrect;
use VSV\GVQ_API\Statistics\Repositories\QuestionCounterRepository;
use VSV\GVQ_API\Statistics\Repositories\QuestionDifficultyRepository;

class QuestionDifficultyProjector implements EventListener
{
    /**
     * @var QuestionCounterRepository
     */
    private $questionAnsweredCorrectRepository;

    /**
     * @var QuestionCounterRepository
     */
    private $questionAnsweredInCorrectRepository;

    /**
     * @var QuestionDifficultyRepository
     */
    private $questionDifficultyRepository;

    /**
     * @param QuestionDifficultyRepository $questionDifficultyRepository
     * @param QuestionCounterRepository $questionAnsweredCorrectRepository
     * @param QuestionCounterRepository $questionAnsweredInCorrectRepository
     */
    public function __construct(
        QuestionDifficultyRepository $questionDifficultyRepository,
        QuestionCounterRepository $questionAnsweredCorrectRepository,
        QuestionCounterRepository $questionAnsweredInCorrectRepository
    ) {
        $this->questionDifficultyRepository = $questionDifficultyRepository;
        $this->questionAnsweredCorrectRepository = $questionAnsweredCorrectRepository;
        $this->questionAnsweredInCorrectRepository = $questionAnsweredInCorrectRepository;
    }

    /**
     * @inheritdoc
     */
    public function handle(DomainMessage $domainMessage)
    {
        $payload = $domainMessage->getPayload();

        if ($payload instanceof AnsweredCorrect) {
            $this->questionAnsweredCorrectRepository->increment($payload->getQuestion());
            $this->questionDifficultyRepository->update($payload->getQuestion());
        } elseif ($payload instanceof AnsweredIncorrect) {
            $this->questionAnsweredInCorrectRepository->increment($payload->getQuestion());
            $this->questionDifficultyRepository->update($payload->getQuestion());
        }
    }
}
