<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use VSV\GVQ_API\Question\Serializers\AnswerDenormalizer;
use VSV\GVQ_API\Question\Serializers\QuestionDenormalizer;

abstract class AbstractAnsweredEventDenormalizer implements DenormalizerInterface
{
    /**
     * @var QuestionDenormalizer
     */
    protected $questionDenormalizer;

    /**
     * @var AnswerDenormalizer
     */
    protected $answerDenormalizer;

    /**
     * @param QuestionDenormalizer $questionDenormalizer
     * @param AnswerDenormalizer $answerDenormalizer
     */
    public function __construct(
        QuestionDenormalizer $questionDenormalizer,
        AnswerDenormalizer $answerDenormalizer
    ) {
        $this->questionDenormalizer = $questionDenormalizer;
        $this->answerDenormalizer = $answerDenormalizer;
    }
}
