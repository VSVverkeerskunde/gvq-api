<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListener;
use VSV\GVQ_API\Quiz\Events\QuizFinished;
use VSV\GVQ_API\Quiz\Repositories\QuizRepository;
use VSV\GVQ_API\Statistics\Repositories\UniqueParticipantRepository;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
use VSV\GVQ_API\Quiz\ValueObjects\StatisticsKey;

class UniqueParticipantProjector implements EventListener
{
    /**
     * @var UniqueParticipantRepository
     */
    private $uniqueParticipantRepository;

    /**
     * @var QuizRepository
     */
    private $quizRepository;

    /**
     * @param UniqueParticipantRepository $uniqueParticipantRepository
     * @param QuizRepository $quizRepository
     */
    public function __construct(
        UniqueParticipantRepository $uniqueParticipantRepository,
        QuizRepository $quizRepository
    ) {
        $this->uniqueParticipantRepository = $uniqueParticipantRepository;
        $this->quizRepository = $quizRepository;
    }

    /**
     * @param DomainMessage $domainMessage
     */
    public function handle(DomainMessage $domainMessage)
    {
        $payload = $domainMessage->getPayload();

        if ($payload instanceof QuizFinished) {
            $quiz = $this->quizRepository->getById($payload->getId());
            $statisticsKey = StatisticsKey::createFromQuiz($quiz);

            $this->uniqueParticipantRepository->add(
                StatisticsKey::createFromQuiz($quiz),
                $quiz->getParticipant()
            );

            if ($quiz->getChannel()->toNative() === QuizChannel::PARTNER) {
                $this->uniqueParticipantRepository->addForPartner(
                    $statisticsKey,
                    $quiz->getParticipant(),
                    $quiz->getPartner()
                );
            }
        }
    }
}
