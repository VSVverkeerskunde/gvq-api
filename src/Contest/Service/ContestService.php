<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Service;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Contest\Models\ContestParticipation;
use VSV\GVQ_API\Contest\Repositories\ContestParticipationRepository;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Quiz\Repositories\QuestionResultRepository;
use VSV\GVQ_API\Quiz\Repositories\QuizRepository;

class ContestService
{
    /**
     * @var QuestionResultRepository
     */
    private $questionResultRepository;

    /**
     * @var QuizRepository
     */
    private $quizRepository;

    /**
     * @var ContestParticipationRepository
     */
    private $contestParticipationRepository;

    /**
     * @param QuestionResultRepository $questionResultRepository
     * @param QuizRepository $quizRepository
     * @param ContestParticipationRepository $contestParticipationRepository
     */
    public function __construct(
        QuestionResultRepository $questionResultRepository,
        QuizRepository $quizRepository,
        ContestParticipationRepository $contestParticipationRepository
    ) {
        $this->questionResultRepository = $questionResultRepository;
        $this->quizRepository = $quizRepository;
        $this->contestParticipationRepository = $contestParticipationRepository;
    }

    /**
     * Can only participate if 11 or more and no previous participation for given channel.
     *
     * @param Year $year
     * @param UuidInterface $quizId
     * @return bool
     */
    public function canParticipate(Year $year, UuidInterface $quizId): bool
    {
        $questionResult = $this->questionResultRepository->getById($quizId);
        if ($questionResult->getScore() < 11) {
            return false;
        }

        $quiz = $this->quizRepository->getById($quizId);
        $contestParticipation = $this->contestParticipationRepository->getByYearAndEmailAndChannel(
            $year,
            $quiz->getParticipant()->getEmail(),
            $quiz->getChannel()
        );

        return $contestParticipation === null;
    }

    /**
     * @param ContestParticipation $contestParticipation
     */
    public function save(ContestParticipation $contestParticipation): void
    {
        $existingParticipation = $this->contestParticipationRepository->getByYearAndEmailAndChannel(
            $contestParticipation->getYear(),
            $contestParticipation->getContestParticipant()->getEmail(),
            $contestParticipation->getChannel()
        );

        if ($existingParticipation === null) {
            $this->contestParticipationRepository->save($contestParticipation);
        }
    }
}
