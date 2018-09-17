<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListener;
use VSV\GVQ_API\Quiz\Events\QuizFinished;
use VSV\GVQ_API\Quiz\Repositories\QuizRepository;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
use VSV\GVQ_API\Statistics\Repositories\TeamParticipationRepository;

class TeamParticipationsProjector implements EventListener
{
    /**
     * @var TeamParticipationRepository
     */
    private $teamParticipationRepository;

    /**
     * @var QuizRepository
     */
    private $quizRepository;

    /**
     * @param TeamParticipationRepository $teamParticipationRepository
     * @param QuizRepository $quizRepository
     */
    public function __construct(
        TeamParticipationRepository $teamParticipationRepository,
        QuizRepository $quizRepository
    ) {
        $this->teamParticipationRepository = $teamParticipationRepository;
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
            if ($quiz->getChannel()->toNative() === QuizChannel::CUP &&
                $quiz->getTeam() !== null) {
                $this->teamParticipationRepository->incrementCountForTeam($quiz->getTeam());
            }
        }
    }
}
