<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Serializers;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactoryInterface;
use VSV\GVQ_API\Factory\ModelsFactory;

class QuestionJsonEnricherTest extends TestCase
{
    /**
     * @var UuidFactoryInterface|MockObject
     */
    private $uuidFactory;

    /**
     * @var QuestionJsonEnricher
     */
    private $questionJsonEnricher;

    public function setUp(): void
    {
        /** @var UuidFactoryInterface|MockObject $uuidFactory */
        $uuidFactory = $this->createMock(UuidFactoryInterface::class);
        $this->uuidFactory = $uuidFactory;

        $this->questionJsonEnricher = new QuestionJsonEnricher(
            $this->uuidFactory
        );
    }

    /**
     * @test
     */
    public function it_can_enrich_a_new_json_question()
    {
        $newJsonQuestion = ModelsFactory::createJson('new_question');

        $this->uuidFactory->expects($this->exactly(4))
            ->method('uuid4')
            ->willReturnOnConsecutiveCalls(
                Uuid::fromString('448c6bd8-0075-4302-a4de-fe34d1554b8d'),
                Uuid::fromString('73e6a2d0-3a50-4089-b84a-208092aeca8e'),
                Uuid::fromString('96bbb677-0839-46ae-9554-bcb709e49cab'),
                Uuid::fromString('53780149-4ef9-405f-b4f4-45e55fde3d67')
            );

        $actualJsonQuestion = $this->questionJsonEnricher->enrich($newJsonQuestion);

        $expectedJsonQuestion = ModelsFactory::createJson('question');
        $this->assertEquals(
            json_decode($expectedJsonQuestion, true),
            json_decode($actualJsonQuestion, true)
        );
    }
}
