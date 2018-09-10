<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListener;
use VSV\GVQ_API\Quiz\Events\QuizFinished;
use VSV\GVQ_API\Quiz\Repositories\QuizRepository;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
use VSV\GVQ_API\Statistics\Repositories\TeamTotalScoreRepository;

class TeamTotalScoreProjector implements EventListener
{
    /**
     * @var TeamTotalScoreRepository
     */
    private $teamTotalScoreRepository;

    /**
     * @var QuizRepository
     */
    private $quizRepository;

    /**
     * @param TeamTotalScoreRepository $teamTotalScoreRepository
     * @param QuizRepository $quizRepository
     */
    public function __construct(TeamTotalScoreRepository $teamTotalScoreRepository, QuizRepository $quizRepository)
    {
        $this->teamTotalScoreRepository = $teamTotalScoreRepository;
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
                $this->teamTotalScoreRepository->incrementCountByQuizScore($quiz->getTeam(), $payload->getScore());
            }
        }
    }
}
