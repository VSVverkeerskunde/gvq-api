<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Question\Serializers\AnswerDenormalizer;
use VSV\GVQ_API\Question\Serializers\AnswerNormalizer;
use VSV\GVQ_API\Question\Serializers\CategoryDenormalizer;
use VSV\GVQ_API\Question\Serializers\CategoryNormalizer;
use VSV\GVQ_API\Question\Serializers\QuestionDenormalizer;
use VSV\GVQ_API\Question\Serializers\QuestionNormalizer;

abstract class AbstractAnsweredEventSerializerTest extends TestCase
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    protected function setUp(): void
    {
        $answerNormalizer = new AnswerNormalizer();
        $questionNormalizer = new QuestionNormalizer(
            new CategoryNormalizer(),
            $answerNormalizer
        );

        $answerDenormalizer = new AnswerDenormalizer();
        $questionDenormalizer = new QuestionDenormalizer(
            new CategoryDenormalizer(),
            $answerDenormalizer
        );

        $normalizers = [
            new AnsweredCorrectNormalizer(
                $questionNormalizer,
                $answerNormalizer
            ),
            new AnsweredCorrectDenormalizer(
                $questionDenormalizer,
                $answerDenormalizer
            ),
            new AnsweredIncorrectNormalizer(
                $questionNormalizer,
                $answerNormalizer
            ),
            new AnsweredIncorrectDenormalizer(
                $questionDenormalizer,
                $answerDenormalizer
            ),
        ];

        $encoders = [
            new JsonEncoder(),
        ];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @return string
     */
    abstract protected function getRepositoryName(): string;
}
