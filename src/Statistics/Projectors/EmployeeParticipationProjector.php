<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListener;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Quiz\Events\QuizFinished;
use VSV\GVQ_API\Quiz\Repositories\QuizRepository;
use VSV\GVQ_API\Statistics\EmployeeParticipation;
use VSV\GVQ_API\Statistics\Repositories\EmployeeParticipationRepository;

class EmployeeParticipationProjector implements EventListener
{
    /**
     * @var EmployeeParticipationRepository
     */
    private $employeeParticipations;
    /**
     * @var QuizRepository
     */
    private $quizzes;

    /**
     * @param EmployeeParticipationRepository $employeeParticipations
     * @param QuizRepository $quizzes
     */
    public function __construct(
        EmployeeParticipationRepository $employeeParticipations,
        QuizRepository $quizzes
    ) {
        $this->employeeParticipations = $employeeParticipations;
        $this->quizzes = $quizzes;
    }

    public function handle(DomainMessage $domainMessage)
    {
        $payload = $domainMessage->getPayload();

        if ($payload instanceof QuizFinished) {
            $quiz = $this->quizzes->getById($payload->getId());
            $company = $quiz->getCompany();

            if ($company instanceof Company) {
                $this->employeeParticipations->save(
                    new EmployeeParticipation($company->getId(), $quiz->getParticipant()->getEmail())
                );
            }
        }
    }
}
