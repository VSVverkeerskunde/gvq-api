<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Events;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class AbstractQuizEventTest extends TestCase
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var AbstractQuizEvent|MockObject
     */
    private $quizEvent;

    /**
     * @throws \ReflectionException
     */
    protected function setUp(): void
    {
        $this->id = Uuid::fromString('5883a592-6d85-4050-8ff2-2c6bd243f903');

        /** @var AbstractQuizEvent|MockObject $quizEvent */
        $quizEvent = $this->getMockForAbstractClass(
            AbstractQuizEvent::class,
            [
                $this->id,
            ]
        );
        $this->quizEvent = $quizEvent;
    }

    /**
     * @test
     */
    public function it_stores_and_id()
    {
        $this->assertEquals(
            $this->id,
            $this->quizEvent->getId()
        );
    }
}
