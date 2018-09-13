<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListener;
use VSV\GVQ_API\Quiz\Events\QuizFinished;
use VSV\GVQ_API\Quiz\Repositories\QuizRepository;
use VSV\GVQ_API\Statistics\Models\EmployeeParticipation;
use VSV\GVQ_API\Statistics\Repositories\EmployeeParticipationRepository;

class EmployeeParticipationProjector implements EventListener
{
    /**
     * @var EmployeeParticipationRepository
     */
    private $employeeParticipationRepository;
    /**
     * @var QuizRepository
     */
    private $quizRepository;

    /**
     * @param EmployeeParticipationRepository $employeeParticipationRepository
     * @param QuizRepository $quizRepository
     */
    public function __construct(
        EmployeeParticipationRepository $employeeParticipationRepository,
        QuizRepository $quizRepository
    ) {
        $this->employeeParticipationRepository = $employeeParticipationRepository;
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
            $company = $quiz->getCompany();

            if ($company) {
                $this->employeeParticipationRepository->save(
                    new EmployeeParticipation(
                        $company->getId(),
                        $quiz->getParticipant()->getEmail()
                    )
                );
            }
        }
    }
}
