<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListener;
use VSV\GVQ_API\Quiz\Events\QuizFinished;
use VSV\GVQ_API\Quiz\Repositories\QuizRepository;
use VSV\GVQ_API\Statistics\Models\DetailedTopScore;
use VSV\GVQ_API\Statistics\Repositories\DetailedTopScoreRepository;
use VSV\GVQ_API\Statistics\Repositories\TopScoreRepository;
use VSV\GVQ_API\Statistics\Models\TopScore;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;

class TopScoresProjector implements EventListener
{
    /**
     * @var TopScoreRepository
     */
    private $topScoreRepository;

    /**
     * @var DetailedTopScoreRepository
     */
    private $detailedTopScoreRepository;

    /**
     * @var QuizRepository
     */
    private $quizRepository;

    /**
     * @param TopScoreRepository $topScoreRepository
     * @param DetailedTopScoreRepository $detailedTopScoreRepository
     * @param QuizRepository $quizRepository
     */
    public function __construct(
        TopScoreRepository $topScoreRepository,
        DetailedTopScoreRepository $detailedTopScoreRepository,
        QuizRepository $quizRepository
    ) {
        $this->topScoreRepository = $topScoreRepository;
        $this->detailedTopScoreRepository = $detailedTopScoreRepository;
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

            $this->topScoreRepository->saveWhenHigher(
                new TopScore(
                    $quiz->getParticipant()->getEmail(),
                    new NaturalNumber($payload->getScore())
                )
            );

            $this->detailedTopScoreRepository->saveWhenHigher(
                new DetailedTopScore(
                    $quiz->getParticipant()->getEmail(),
                    $quiz->getLanguage(),
                    $quiz->getChannel(),
                    new NaturalNumber($payload->getScore())
                )
            );
        }
    }
}
