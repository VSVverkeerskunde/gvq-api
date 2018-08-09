<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Events;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\Models\Answer;
use VSV\GVQ_API\Question\Models\Question;

class AbstractAnsweredEventTest extends TestCase
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var Question
     */
    private $question;

    /**
     * @var Answer
     */
    private $answer;

    /**
     * @var \DateTimeImmutable
     */
    private $answeredOn;

    /**
     * @var AbstractAnsweredEvent|MockObject
     */
    private $answeredEvent;

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $this->id = Uuid::fromString('5883a592-6d85-4050-8ff2-2c6bd243f903');
        $this->question = ModelsFactory::createAccidentQuestion();
        $this->answer = $this->question->getAnswers()->toArray()[0];
        $this->answeredOn = new \DateTimeImmutable();

        /** @var AbstractAnsweredEvent|MockObject $answeredEvent */
        $answeredEvent = $this->getMockForAbstractClass(
            AbstractAnsweredEvent::class,
            [
                $this->id,
                $this->question,
                $this->answer,
                $this->answeredOn
            ]
        );
        $this->answeredEvent = $answeredEvent;
    }

    /**
     * @test
     */
    public function it_stores_and_id()
    {
        $this->assertEquals(
            $this->id,
            $this->answeredEvent->getId()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_question()
    {
        $this->assertEquals(
            $this->question,
            $this->answeredEvent->getQuestion()
        );
    }

    /**
     * @test
     */
    public function it_stores_an_answer()
    {
        $this->assertEquals(
            $this->answer,
            $this->answeredEvent->getAnswer()
        );
    }

    /**
     * @test
     */
    public function it_stores_an_answer_on()
    {
        $this->assertEquals(
            $this->answeredOn,
            $this->answeredEvent->getAnsweredOn()
        );
    }
}
