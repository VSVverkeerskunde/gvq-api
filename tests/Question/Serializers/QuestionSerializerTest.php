<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Serializers;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\Models\Question;

class QuestionSerializerTest extends TestCase
{
    /**
     * @var QuestionSerializer
     */
    private $serializer;

    /**
     * @var string
     */
    private $questionAsJson;

    /**
     * @var Question
     */
    private $question;

    protected function setUp(): void
    {
        $this->serializer = new QuestionSerializer();

        $this->questionAsJson = ModelsFactory::createJson('question');
        $this->question = ModelsFactory::createAccidentQuestion();
    }

    /**
     * @test
     */
    public function it_can_serialize_to_json(): void
    {
        $actualJson = $this->serializer->serialize(
            $this->question,
            'json'
        );

        $this->assertEquals(
            $this->questionAsJson,
            $actualJson
        );
    }

    /**
     * @test
     */
    public function it_can_deserialize_to_question(): void
    {
        $actualQuestion = $this->serializer->deserialize(
            $this->questionAsJson,
            Question::class,
            'json'
        );

        $this->assertEquals(
            $this->question,
            $actualQuestion
        );
    }

    /**
     * @test
     */
    public function it_can_deserialize_to_question_when_ids_are_missing(): void
    {
        $questionAsJson = ModelsFactory::createJson('question_with_missing_ids');

        /** @var Question $actualQuestion */
        $actualQuestion = $this->serializer->deserialize(
            $questionAsJson,
            Question::class,
            'json'
        );

        $this->assertNotNull($actualQuestion->getId());
        foreach ($actualQuestion->getAnswers() as $answer) {
            $this->assertNotNull($answer->getId());
        }
    }
}
