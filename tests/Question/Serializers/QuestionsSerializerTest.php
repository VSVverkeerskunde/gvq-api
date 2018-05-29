<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Serializers;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\Models\Questions;

class QuestionsSerializerTest extends TestCase
{
    /**
     * @var QuestionsSerializer
     */
    private $serializer;

    /**
     * @var string
     */
    private $questionsAsJson;

    /**
     * @var Questions
     */
    private $questions;

    protected function setUp(): void
    {
        $this->serializer = new QuestionsSerializer();

        $this->questionsAsJson = ModelsFactory::createJson('questions');
        $this->questions = ModelsFactory::createQuestions();
    }

    /**
     * @test
     */
    public function it_can_serialize_to_json(): void
    {
        $actualJson = $this->serializer->serialize(
            $this->questions,
            'json'
        );

        $this->assertEquals(
            $this->questionsAsJson,
            $actualJson
        );
    }
}
