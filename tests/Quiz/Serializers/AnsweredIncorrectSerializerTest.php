<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\Events\AnsweredIncorrect;

class AnsweredIncorrectSerializerTest extends AbstractAnsweredEventSerializerTest
{
    /**
     * @inheritdoc
     */
    protected function getRepositoryName(): string
    {
        return AnsweredIncorrect::class;
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_serialize_to_json(): void
    {
        $answeredIncorrectAsJson = ModelsFactory::createJson('answered_incorrect');
        $answeredIncorrect = ModelsFactory::createAnsweredIncorrect();

        $actualJson = $this->serializer->serialize(
            $answeredIncorrect,
            'json'
        );

        $this->assertEquals(
            $answeredIncorrectAsJson,
            $actualJson
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_deserialize_to_answered_incorrect(): void
    {
        $answeredIncorrectAsJson = ModelsFactory::createJson('answered_incorrect');
        $answeredIncorrect = ModelsFactory::createAnsweredIncorrect();

        $actualAnsweredCorrect = $this->serializer->deserialize(
            $answeredIncorrectAsJson,
            AnsweredIncorrect::class,
            'json'
        );

        $this->assertEquals(
            $answeredIncorrect,
            $actualAnsweredCorrect
        );
    }
}
