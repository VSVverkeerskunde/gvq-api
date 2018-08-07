<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\Events\AnsweredCorrect;

class AnsweredCorrectSerializerTest extends AbstractAnsweredEventSerializerTest
{
    /**
     * @inheritdoc
     */
    protected function getRepositoryName(): string
    {
        return AnsweredCorrect::class;
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_serialize_to_json(): void
    {
        $answeredCorrectAsJson = ModelsFactory::createJson('answered_correct');
        $answeredCorrect = ModelsFactory::createAnsweredCorrect();

        $actualJson = $this->serializer->serialize(
            $answeredCorrect,
            'json'
        );

        $this->assertEquals(
            $answeredCorrectAsJson,
            $actualJson
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_deserialize_to_answered_correct(): void
    {
        $answeredCorrectAsJson = ModelsFactory::createJson('answered_correct');
        $answeredCorrect = ModelsFactory::createAnsweredCorrect();

        $actualAnsweredCorrect = $this->serializer->deserialize(
            $answeredCorrectAsJson,
            AnsweredCorrect::class,
            'json'
        );

        $this->assertEquals(
            $answeredCorrect,
            $actualAnsweredCorrect
        );
    }
}
