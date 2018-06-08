<?php declare(strict_types=1);


namespace VSV\GVQ_API\Question\Serializers;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
use VSV\GVQ_API\Question\Models\Answer;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;

class AnswerNormalizerTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider dataProvider
     * @param Answer|NotEmptyString $data
     * @param string $format
     */
    public function it_only_supports_answer_type_and_json_format(
        $data,
        string $format
    ): void {
        $answerNormalizer = new AnswerNormalizer();

        $this->assertFalse(
            $answerNormalizer->supportsNormalization(
                $data,
                $format
            )
        );
    }

    /**
     * @return array
     */
    public function dataProvider(): array
    {
        return [
            [
                new Answer(
                    Uuid::fromString('b7322f69-98cf-4ec4-a551-5d6661fffc17'),
                    new PositiveNumber(1),
                    new NotEmptyString('This is the first answer.'),
                    true
                ),
                'xml'
            ],
            [
                new NotEmptyString('This is the first answer.'),
                'json'
            ]
        ];
    }
}
