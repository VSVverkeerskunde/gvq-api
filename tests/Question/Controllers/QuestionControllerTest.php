<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Controllers;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Common\Serializers\JsonEnricher;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Repositories\QuestionRepository;

class QuestionControllerTest extends TestCase
{
    /**
     * @var QuestionRepository|MockObject
     */
    private $questionRepository;

    /**
     * @var SerializerInterface|MockObject
     */
    private $serializer;

    /**
     * @var UuidFactoryInterface|MockObject
     */
    private $uuidFactory;

    /**
     * @var JsonEnricher|MockObject
     */
    private $jsonEnricher;

    /**
     * @var QuestionController
     */
    private $questionController;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        /** @var QuestionRepository|MockObject $questionRepository */
        $questionRepository = $this->createMock(QuestionRepository::class);
        $this->questionRepository = $questionRepository;

        /** @var SerializerInterface|MockObject $serializer */
        $serializer = $this->createMock(SerializerInterface::class);
        $this->serializer = $serializer;

        /** @var UuidFactoryInterface|MockObject $uuidFactory */
        $uuidFactory = $this->createMock(UuidFactoryInterface::class);
        $this->uuidFactory = $uuidFactory;

        /** @var JsonEnricher|MockObject $jsonEnricher */
        $jsonEnricher = $this->createMock(JsonEnricher::class);
        $this->jsonEnricher = $jsonEnricher;

        $this->questionController = new QuestionController(
            $this->questionRepository,
            $this->serializer,
            $this->uuidFactory,
            $this->jsonEnricher
        );
    }

    /**
     * @test
     */
    public function it_saves_a_question(): void
    {
        $newQuestionJson = ModelsFactory::createJson('new_question');
        $questionJson = ModelsFactory::createJson('question');

        $this->jsonEnricher->expects($this->once())
            ->method('enrich')
            ->with($newQuestionJson)
            ->willReturn($questionJson);

        $question = ModelsFactory::createAccidentQuestion();
        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->with(
                $questionJson,
                Question::class,
                'json'
            )
            ->willReturn(
                $question
            );

        $this->questionRepository
            ->expects($this->once())
            ->method('save')
            ->with($question);

        $request = new Request([], [], [], [], [], [], $newQuestionJson);
        $actualResponse = $this->questionController->save($request);

        $this->assertEquals(
            '{"id":"'.$question->getId()->toString().'"}',
            $actualResponse->getContent()
        );
        $this->assertEquals(
            'application/json',
            $actualResponse->headers->get('Content-Type')
        );
    }

    /**
     * @test
     */
    public function it_can_delete_a_question(): void
    {
        $uuid = Uuid::fromString('448c6bd8-0075-4302-a4de-fe34d1554b8d');

        $this->uuidFactory->expects($this->once())
            ->method('fromString')
            ->with('448c6bd8-0075-4302-a4de-fe34d1554b8d')
            ->willReturn($uuid);

        $this->questionRepository->expects($this->once())
            ->method('delete')
            ->with($uuid);

        $actualResponse = $this->questionController->delete($uuid->toString());

        $this->assertEquals(
            '{"id":"448c6bd8-0075-4302-a4de-fe34d1554b8d"}',
            $actualResponse->getContent()
        );
        $this->assertEquals(
            'application/json',
            $actualResponse->headers->get('Content-Type')
        );
    }

    /**
     * @test
     */
    public function it_can_get_all_questions(): void
    {
        $questions = ModelsFactory::createQuestions();
        $questionsJson = ModelsFactory::createJson('questions');

        $this->questionRepository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn($questions);

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(
                $questions,
                'json'
            )
            ->willReturn(
                $questionsJson
            );

        $actualResponse = $this->questionController->getAll();

        $this->assertEquals(
            $questionsJson,
            $actualResponse->getContent()
        );
        $this->assertEquals(
            'application/json',
            $actualResponse->headers->get('Content-Type')
        );
    }

    /**
     * @test
     */
    public function it_returns_an_empty_array_when_no_questions_found(): void
    {
        $this->questionRepository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn(null);

        $actualResponse = $this->questionController->getAll();

        $this->assertEquals(
            '[]',
            $actualResponse->getContent()
        );
        $this->assertEquals(
            'application/json',
            $actualResponse->headers->get('Content-Type')
        );
    }
}
