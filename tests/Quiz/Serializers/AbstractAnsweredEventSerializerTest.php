<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use PHPUnit\Framework\TestCase;
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
     * @var QuestionNormalizer
     */
    protected $questionNormalizer;

    /**
     * @var AnswerNormalizer
     */
    protected $answerNormalizer;

    /**
     * @var QuestionDenormalizer
     */
    protected $questionDenormalizer;

    /**
     * @var AnswerDenormalizer
     */
    protected $answerDenormalizer;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    protected function setUp(): void
    {
        $this->answerNormalizer = new AnswerNormalizer();
        $this->questionNormalizer = new QuestionNormalizer(
            new CategoryNormalizer(),
            $this->answerNormalizer
        );

        $this->answerDenormalizer = new AnswerDenormalizer();
        $this->questionDenormalizer = new QuestionDenormalizer(
            new CategoryDenormalizer(),
            $this->answerDenormalizer
        );
    }

    /**
     * @return string
     */
    abstract protected function getRepositoryName(): string;
}
