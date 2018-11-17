<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Service;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Contest\Models\ContestParticipation;
use VSV\GVQ_API\Contest\Models\ContestParticipations;
use VSV\GVQ_API\Contest\Repositories\ContestParticipationRepository;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Quiz\Repositories\QuestionResultRepository;
use VSV\GVQ_API\Quiz\Repositories\QuizRepository;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;

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
     * @var bool
     */
    private $closed;

    /**
     * @param QuestionResultRepository $questionResultRepository
     * @param QuizRepository $quizRepository
     * @param ContestParticipationRepository $contestParticipationRepository
     * @param bool $closed
     */
    public function __construct(
        QuestionResultRepository $questionResultRepository,
        QuizRepository $quizRepository,
        ContestParticipationRepository $contestParticipationRepository,
        bool $closed = false
    ) {
        $this->questionResultRepository = $questionResultRepository;
        $this->quizRepository = $quizRepository;
        $this->contestParticipationRepository = $contestParticipationRepository;
        $this->closed = $closed;
    }

    /**
     * @return ContestParticipations|null
     */
    public function getAll(): ?ContestParticipations
    {
        return $this->contestParticipationRepository->getAll();
    }

    /**
     * @return \Traversable
     */
    public function getTraversableContestParticipations(): \Traversable
    {
        return $this->contestParticipationRepository->getAllAsTraversable();
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
        if ($this->closed) {
            return false;
        }

        $questionResult = $this->questionResultRepository->getById($quizId);
        if ($questionResult->getScore() < 11) {
            return false;
        }

        $quiz = $this->quizRepository->getById($quizId);
        $channel = $quiz->getChannel();
        if ($channel->toNative() !== QuizChannel::CUP) {
            $channel = new QuizChannel(QuizChannel::INDIVIDUAL);
        }

        $contestParticipation = $this->contestParticipationRepository->getByYearAndEmailAndChannel(
            $year,
            $quiz->getParticipant()->getEmail(),
            $channel
        );

        return $contestParticipation === null;
    }

    /**
     * @param ContestParticipation $contestParticipation
     */
    public function save(ContestParticipation $contestParticipation): void
    {
        if ($this->closed) {
            return;
        }

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
