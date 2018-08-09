<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Question\Serializers\AnswerNormalizer;
use VSV\GVQ_API\Question\Serializers\QuestionNormalizer;
use VSV\GVQ_API\Quiz\Events\AbstractAnsweredEvent;
use VSV\GVQ_API\Quiz\ValueObjects\QuestionResult;

abstract class AbstractAnsweredEventNormalizer implements NormalizerInterface
{
    /**
     * @var QuestionResultNormalizer
     */
    private $questionResultNormalizer;

    /**
     * @var AnswerNormalizer
     */
    private $answerNormalizer;

    /**
     * @param QuestionResultNormalizer $questionResultNormalizer
     * @param AnswerNormalizer $answerNormalizer
     */
    public function __construct(
        QuestionResultNormalizer $questionResultNormalizer,
        AnswerNormalizer $answerNormalizer
    ) {
        $this->questionResultNormalizer = $questionResultNormalizer;
        $this->answerNormalizer = $answerNormalizer;
    }

    /**
     * @inheritdoc
     */
    public function normalize($answeredEvent, $format = null, array $context = []): array
    {
        return [
            'id' => $answeredEvent->getId()->toString(),
            'questionResult' => $this->questionResultNormalizer->normalize($answeredEvent->getQuestionResult()),
            'answer' => $this->answerNormalizer->normalize($answeredEvent->getAnswer()),
            'answeredOn' => $answeredEvent->getAnsweredOn()->format(DATE_ATOM),
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof AbstractAnsweredEvent) && ($format === 'json');
    }
}
