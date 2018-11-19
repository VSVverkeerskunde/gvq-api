<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListener;
use VSV\GVQ_API\Quiz\Events\QuizFinished;
use VSV\GVQ_API\Quiz\Repositories\QuizRepository;
use VSV\GVQ_API\Statistics\Models\TeamParticipant;
use VSV\GVQ_API\Statistics\Repositories\TeamParticipantRepository;

class TeamParticipantProjector implements EventListener
{
    /**
     * @var TeamParticipantRepository
     */
    private $teamParticipantRepository;

    /**
     * @var QuizRepository
     */
    private $quizRepository;

    /**
     * @param TeamParticipantRepository $teamParticipationRepository
     * @param QuizRepository $quizRepository
     */
    public function __construct(
        TeamParticipantRepository $teamParticipationRepository,
        QuizRepository $quizRepository
    ) {
        $this->teamParticipantRepository = $teamParticipationRepository;
        $this->quizRepository = $quizRepository;
    }

    /**
     * @param DomainMessage $domainMessage
     */
    public function handle(DomainMessage $domainMessage): void
    {
        $payload = $domainMessage->getPayload();

        if ($payload instanceof QuizFinished) {
            $quiz = $this->quizRepository->getById($payload->getId());
            $team = $quiz->getTeam();

            if ($team) {
                $this->teamParticipantRepository->save(
                    new TeamParticipant(
                        $team->getId(),
                        $quiz->getParticipant()->getEmail()
                    )
                );
            }
        }
    }
}
