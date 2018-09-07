<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Events;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\Models\Question;

class AnsweredTooLateTest extends TestCase
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
     * @var \DateTimeImmutable
     */
    private $answeredOn;

    /**
     * @var AnsweredTooLate
     */
    private $answeredTooLate;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $this->id = Uuid::fromString('13992967-983a-415a-9499-fe7d76236a91');
        $this->question = ModelsFactory::createAccidentQuestion();
        $this->answeredOn = new \DateTimeImmutable();

        $this->answeredTooLate = new AnsweredTooLate(
            $this->id,
            $this->question,
            $this->answeredOn
        );
    }

    /**
     * @test
     */
    public function it_stores_an_id()
    {
        $this->assertEquals(
            $this->id,
            $this->answeredTooLate->getId()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_question()
    {
        $this->assertEquals(
            $this->question,
            $this->answeredTooLate->getQuestion()
        );
    }

    /**
     * @test
     */
    public function it_stores_an_asked_on()
    {
        $this->assertEquals(
            $this->answeredOn,
            $this->answeredTooLate->getAnsweredOn()
        );
    }
}
