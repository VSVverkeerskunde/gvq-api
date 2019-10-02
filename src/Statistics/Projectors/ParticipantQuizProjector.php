<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListener;
use VSV\GVQ_API\Quiz\Events\QuizStarted;
use VSV\GVQ_API\Quiz\Repositories\QuizRepository;
use VSV\GVQ_API\Statistics\Repositories\Entities\ParticipantQuizEntity;
use VSV\GVQ_API\Statistics\Repositories\ParticipantQuizDoctrineRepository;

class ParticipantQuizProjector implements EventListener
{
    /**
     * @var ParticipantQuizDoctrineRepository
     */
    private $participantQuizDoctrineRepository;

    /**
     * @param TeamParticipantRepository $teamParticipationRepository
     * @param QuizRepository $quizRepository
     */
    public function __construct(
        ParticipantQuizDoctrineRepository $participantQuizDoctrineRepository
    ) {
        $this->participantQuizDoctrineRepository = $participantQuizDoctrineRepository;
    }

    /**
     * @param DomainMessage $domainMessage
     */
    public function handle(DomainMessage $domainMessage): void
    {
        $payload = $domainMessage->getPayload();

        if ($payload instanceof QuizStarted) {
            $email = $payload->getQuiz()->getParticipant()->getEmail()->toNative();

            $this->participantQuizDoctrineRepository->save(
                new ParticipantQuizEntity(
                    $email,
                    $payload->getQuiz()->getId()->toString()
                )
            );
        }
    }
}
