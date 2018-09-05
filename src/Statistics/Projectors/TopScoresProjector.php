<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListener;
use VSV\GVQ_API\Quiz\Events\QuizFinished;
use VSV\GVQ_API\Quiz\Repositories\QuizRepository;
use VSV\GVQ_API\Statistics\Repositories\TopScoreRepository;
use VSV\GVQ_API\Statistics\Models\TopScore;

class TopScoresProjector implements EventListener
{
    /**
     * @var TopScoreRepository
     */
    private $topScores;

    /**
     * @var QuizRepository
     */
    private $quizzes;

    /**
     * @param TopScoreRepository $topScores
     * @param QuizRepository $quizzes
     */
    public function __construct(
        TopScoreRepository $topScores,
        QuizRepository $quizzes
    ) {
        $this->topScores = $topScores;
        $this->quizzes = $quizzes;
    }

    public function handle(DomainMessage $domainMessage)
    {
        $payload = $domainMessage->getPayload();

        if ($payload instanceof QuizFinished) {
            $quiz = $this->quizzes->getById($payload->getId());
            $existingTopScore = $this->topScores->getByEmail($quiz->getParticipant()->getEmail());

            if ($existingTopScore instanceof TopScore && $existingTopScore->getScore() >= $payload->getScore()) {
                return;
            }

            $this->topScores->save(new TopScore($quiz->getParticipant()->getEmail(), $payload->getScore()));
        }
    }
}
