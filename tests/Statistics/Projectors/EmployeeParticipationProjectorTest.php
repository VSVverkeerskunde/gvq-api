<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\Events\QuizFinished;
use VSV\GVQ_API\Quiz\Repositories\QuizRepository;
use VSV\GVQ_API\Statistics\EmployeeParticipation;
use VSV\GVQ_API\Statistics\Repositories\EmployeeParticipationRepository;
use VSV\GVQ_API\User\ValueObjects\Email;

class EmployeeParticipationProjectorTest extends TestCase
{
    /**
     * @var EmployeeParticipationRepository|MockObject
     */
    private $employeeParticipations;

    /**
     * @var QuizRepository|MockObject
     */
    private $quizzes;

    /**
     * @var EmployeeParticipationProjector
     */
    private $projector;

    public function setUp()
    {
        /** @var EmployeeParticipationRepository|MockObject $employeeParticipations */
        $employeeParticipations = $this->createMock(EmployeeParticipationRepository::class);
        /** @var QuizRepository|MockObject $quizzes */
        $quizzes = $this->createMock(QuizRepository::class);

        $this->employeeParticipations = $employeeParticipations;
        $this->quizzes = $quizzes;
        $this->projector = new EmployeeParticipationProjector($employeeParticipations, $quizzes);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_remembers_employee_participation_when_they_finish_a_quiz()
    {
        $quiz = ModelsFactory::createCompanyQuiz();
        $email = new Email('par@ticipa.nt');
        $companyId = Uuid::fromString('85fec50a-71ed-4d12-8a69-28a3cf5eb106');

        $quizFinishedMessage = DomainMessage::recordNow(
            $quiz->getId(),
            0,
            new Metadata(),
            new QuizFinished($quiz->getId(), 16)
        );

        $this->quizzes
            ->expects($this->once())
            ->method('getById')
            ->willReturn($quiz);

        $this->employeeParticipations
            ->expects($this->once())
            ->method('save')
            ->with(new EmployeeParticipation($companyId, $email))
            ->willReturn(null);

        $this->projector->handle($quizFinishedMessage);
    }
}
