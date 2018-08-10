<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Question\Serializers\AnswerNormalizer;
use VSV\GVQ_API\Question\Serializers\QuestionNormalizer;

abstract class AbstractAnsweredEventNormalizer implements NormalizerInterface
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
     * @param QuestionNormalizer $questionNormalizer
     * @param AnswerNormalizer $answerNormalizer
     */
    public function __construct(
        QuestionNormalizer $questionNormalizer,
        AnswerNormalizer $answerNormalizer
    ) {
        $this->questionNormalizer = $questionNormalizer;
        $this->answerNormalizer = $answerNormalizer;
    }
}
