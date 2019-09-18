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

            // All unique participations per channel and language.
            $statisticsKey = StatisticsKey::createFromQuiz($quiz);
            $this->uniqueParticipantRepository->add(
                $statisticsKey,
                $quiz->getParticipant()
            );

            // All unique participations per channel, regardless of language.
            $totalStatisticsKey = StatisticsKey::createChannelTotalFromQuiz($quiz);
            $this->uniqueParticipantRepository->add(
                $totalStatisticsKey,
                $quiz->getParticipant()
            );

            if (
                !$quiz->getChannel()->equals(new QuizChannel(QuizChannel::CUP)) &&
                !$quiz->getChannel()->equals(new QuizChannel(QuizChannel::LEAGUE))
            ) {
                // All unique participations for quiz (not cup or league) and language.
                $quizTotalStatisticsKey = StatisticsKey::createQuizTotalFromQuiz($quiz);
                $this->uniqueParticipantRepository->add(
                    $quizTotalStatisticsKey,
                    $quiz->getParticipant()
                );

                // All unique participations for quiz (not cup or league), regardless of language.
                $this->uniqueParticipantRepository->add(
                    new StatisticsKey(StatisticsKey::QUIZ_TOT),
                    $quiz->getParticipant()
                );
            }

            // Overall unique participations per language.
            $overallTotalStatisticsKey = StatisticsKey::createOverallTotalFromQuiz($quiz);
            $this->uniqueParticipantRepository->add(
                $overallTotalStatisticsKey,
                $quiz->getParticipant()
            );

            // Overall unique participations regardless of language.
            $this->uniqueParticipantRepository->add(
                new StatisticsKey(StatisticsKey::OVERALL_TOT),
                $quiz->getParticipant()
            );

            if ($payload->getScore() >= 11) {
                // All unique participations per channel and language.
                $this->uniqueParticipantRepository->addPassed(
                    $statisticsKey,
                    $quiz->getParticipant()
                );

                // All unique participations per channel, regardless of language.
                $totalStatisticsKey = StatisticsKey::createChannelTotalFromQuiz($quiz);
                $this->uniqueParticipantRepository->addPassed(
                    $totalStatisticsKey,
                    $quiz->getParticipant()
                );

                if (
                    !$quiz->getChannel()->equals(new QuizChannel(QuizChannel::CUP)) &&
                    !$quiz->getChannel()->equals(new QuizChannel(QuizChannel::LEAGUE))
                ) {
                    // All unique participations for quiz (not cup or league) and language.
                    $quizTotalStatisticsKey = StatisticsKey::createQuizTotalFromQuiz($quiz);
                    $this->uniqueParticipantRepository->addPassed(
                        $quizTotalStatisticsKey,
                        $quiz->getParticipant()
                    );

                    // All unique participations for quiz (not cup or league), regardless of language.
                    $this->uniqueParticipantRepository->addPassed(
                        new StatisticsKey(StatisticsKey::QUIZ_TOT),
                        $quiz->getParticipant()
                    );
                }

                // Overall unique participations per language.
                $overallTotalStatisticsKey = StatisticsKey::createOverallTotalFromQuiz($quiz);
                $this->uniqueParticipantRepository->addPassed(
                    $overallTotalStatisticsKey,
                    $quiz->getParticipant()
                );

                // Overall unique participations regardless of language.
                $this->uniqueParticipantRepository->addPassed(
                    new StatisticsKey(StatisticsKey::OVERALL_TOT),
                    $quiz->getParticipant()
                );
            }

            if ($quiz->getChannel()->toNative() === QuizChannel::PARTNER &&
                $quiz->getPartner() !== null) {
                $this->uniqueParticipantRepository->addForPartner(
                    $statisticsKey,
                    $quiz->getParticipant(),
                    $quiz->getPartner()
                );
            }
        }
    }
}
