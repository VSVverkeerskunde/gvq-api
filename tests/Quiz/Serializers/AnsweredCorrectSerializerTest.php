<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use VSV\GVQ_API\Quiz\Events\AnsweredCorrect;

class AnsweredCorrectSerializerTest extends AbstractAnsweredEventSerializerrTest
{

    protected function setUp(): void
    {
        parent::setUp();

        $normalizers = [
            new AnsweredCorrectNormalizer(
                $this->questionNormalizer,
                $this->answerNormalizer
            ),
            new AnsweredCorrectDenormalizer(
                $this->questionDenormalizer,
                $this->answerDenormalizer
            ),
        ];
    }

    protected function getRepositoryName(): string
    {
        return AnsweredCorrect::class;
    }

}
