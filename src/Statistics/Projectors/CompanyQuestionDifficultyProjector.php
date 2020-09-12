<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListener;
use VSV\GVQ_API\Quiz\Events\AnsweredCorrect;
use VSV\GVQ_API\Quiz\Events\AnsweredIncorrect;
use VSV\GVQ_API\Quiz\Repositories\QuizRepository;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
use VSV\GVQ_API\Statistics\Repositories\CompanyQuestionCounterRepositoryFactory;
use VSV\GVQ_API\Statistics\Repositories\CompanyQuestionDifficultyRepositoryFactory;
use VSV\GVQ_API\Statistics\Repositories\EmployeeParticipationRepository;

class CompanyQuestionDifficultyProjector  implements EventListener
{
    /**
     * @var CompanyQuestionCounterRepositoryFactory
     */
    private $questionAnsweredCorrectRepositoryFactory;

    /**
     * @var CompanyQuestionCounterRepositoryFactory
     */
    private $questionAnsweredInCorrectRepositoryFactory;

    /**
     * @var CompanyQuestionDifficultyRepositoryFactory
     */
    private $questionDifficultyRepositoryFactory;

    /**
     * @var QuizRepository
     */
    private $quizRepository;

    /**
     * @var EmployeeParticipationRepository
     */
    private $employeeParticipationRepository;

    /**
     * @param CompanyQuestionDifficultyRepositoryFactory $questionDifficultyRepository
     * @param CompanyQuestionCounterRepositoryFactory $questionAnsweredCorrectRepository
     * @param CompanyQuestionCounterRepositoryFactory $questionAnsweredInCorrectRepository
     * @param QuizRepository $quizRepository
     * @param EmployeeParticipationRepository $employeeParticipationRepository
     */
    public function __construct(
        CompanyQuestionDifficultyRepositoryFactory $questionDifficultyRepositoryFactory,
        CompanyQuestionCounterRepositoryFactory $questionAnsweredCorrectRepositoryFactory,
        CompanyQuestionCounterRepositoryFactory $questionAnsweredInCorrectRepositoryFactory,
        QuizRepository $quizRepository,
        EmployeeParticipationRepository $employeeParticipationRepository
    ) {
        $this->questionDifficultyRepositoryFactory = $questionDifficultyRepositoryFactory;
        $this->questionAnsweredCorrectRepositoryFactory = $questionAnsweredCorrectRepositoryFactory;
        $this->questionAnsweredInCorrectRepositoryFactory = $questionAnsweredInCorrectRepositoryFactory;

        $this->quizRepository = $quizRepository;
        $this->employeeParticipationRepository = $employeeParticipationRepository;
    }

    /**
     * @inheritdoc
     */
    public function handle(DomainMessage $domainMessage)
    {
        $payload = $domainMessage->getPayload();
        if (!($payload instanceof AnsweredCorrect || $payload instanceof AnsweredInCorrect)) {
            return;
        }

        $quiz = $this->quizRepository->getById($payload->getId());
        if (
            $quiz->getChannel()->toNative() === QuizChannel::CUP ||
            $quiz->getChannel()->toNative() === QuizChannel::LEAGUE
        ) {
            return;
        }

        $company = $quiz->getCompany();
        if (!$company) {
            return;
        }

        $companyId = $quiz->getCompany()->getId();

        if ($payload instanceof AnsweredCorrect) {
            $this->questionAnsweredCorrectRepositoryFactory
                ->forCompany($companyId)
                ->increment($payload->getQuestion());
        } elseif ($payload instanceof AnsweredIncorrect) {
            $this->questionAnsweredInCorrectRepositoryFactory
                ->forCompany($companyId)
                ->increment($payload->getQuestion());
        }

        $this->questionDifficultyRepositoryFactory
            ->forCompany($companyId)
            ->update($payload->getQuestion());
    }
}
