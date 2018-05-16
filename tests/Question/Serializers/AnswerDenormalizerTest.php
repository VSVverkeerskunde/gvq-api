<?php declare(strict_types=1);


namespace VSV\GVQ_API\Question\Serializers;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Question\Models\Answer;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;

class AnswerDenormalizerTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider dataProvider
     * @param Answer|NotEmptyString $data
     * @param string $type
     * @param string $format
     */
    public function it_only_supports_answer_type_and_json_format(
        $data,
        string $type,
        string $format
    ): void {
        $answerDenormalizer = new AnswerDenormalizer();

        $this->assertFalse(
            $answerDenormalizer->supportsDenormalization(
                $data,
                $type,
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
                [],
                Answer::class,
                'xml'
            ],
            [
                [],
                NotEmptyString::class,
                'json'
            ]
        ];
    }
}
