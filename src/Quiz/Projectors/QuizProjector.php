<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListener;
use VSV\GVQ_API\Quiz\Events\EmailRegistered;
use VSV\GVQ_API\Quiz\Events\QuizFinished;
use VSV\GVQ_API\Quiz\Events\QuizStarted;
use VSV\GVQ_API\Quiz\Repositories\QuizRepository;
use VSV\GVQ_API\Quiz\ValueObjects\QuizParticipant;

class QuizProjector implements EventListener
{
    /**
     * @var QuizRepository
     */
    private $quizRepository;

    /**
     * @param QuizRepository $quizRepository
     */
    public function __construct(QuizRepository $quizRepository)
    {
        $this->quizRepository = $quizRepository;
    }

    /**
     * @param DomainMessage $domainMessage
     */
    public function handle(DomainMessage $domainMessage)
    {
        $payload = $domainMessage->getPayload();

        if ($payload instanceof QuizStarted) {
            $this->quizRepository->save($payload->getQuiz());
        } elseif ($payload instanceof QuizFinished) {
            $quiz = $this->quizRepository->getById($payload->getId());

            if ($quiz) {
                $quiz = $quiz->withScore(
                    $payload->getScore()
                );

                $this->quizRepository->save($quiz);
            }
        } elseif ($payload instanceof EmailRegistered) {
            $quiz = $this->quizRepository->getById($payload->getId());

            if ($quiz) {
                $quiz = $quiz->withParticipant(
                    new QuizParticipant(
                        $payload->getEmail()
                    )
                );

                $this->quizRepository->save($quiz);
            }
        }
    }
}
