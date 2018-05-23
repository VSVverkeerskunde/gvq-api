<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Controllers;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Repositories\QuestionRepository;
use VSV\GVQ_API\Question\Serializers\ExpectedJsonTrait;
use VSV\GVQ_API\Question\Serializers\QuestionSerializer;
use VSV\GVQ_API\Question\Serializers\QuestionsSerializer;

class QuestionControllerTest extends TestCase
{
    use ExpectedJsonTrait;

    /**
     * @var QuestionRepository|MockObject
     */
    private $questionRepository;

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

        $this->questionController = new QuestionController(
            $this->questionRepository,
            new QuestionSerializer(),
            new QuestionsSerializer()
        );
    }

    /**
     * @test
     */
    public function it_saves_a_question(): void
    {
        $questionJson = $this->getExpectedJson(__DIR__.'/../Serializers/Samples/question.json');
        $request = new Request([], [], [], [], [], [], $questionJson);
        $questionSerializer = new QuestionSerializer();
        /** @var Question $question */
        $question = $questionSerializer->deserialize($questionJson, Question::class, 'json');

        $this->questionRepository
            ->expects($this->once())
            ->method('save')
            ->with($question);
        $expectedResponse = new Response('{"id":"'.$question->getId()->toString().'"}');
        $expectedResponse->headers->set('Content-Type', 'application/json');

        $actualResponse = $this->questionController->save($request);

        $this->assertEquals(
            $expectedResponse->getContent(),
            $actualResponse->getContent()
        );
        $this->assertEquals(
            $expectedResponse->headers->get('Content-Type'),
            $actualResponse->headers->get('Content-Type')
        );
    }
}
