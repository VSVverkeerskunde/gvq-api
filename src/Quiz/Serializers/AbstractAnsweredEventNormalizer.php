<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Question\Serializers\AnswerNormalizer;
use VSV\GVQ_API\Question\Serializers\QuestionNormalizer;
use VSV\GVQ_API\Quiz\Events\AbstractAnsweredEvent;

abstract class AbstractAnsweredEventNormalizer implements NormalizerInterface
{
    /**
     * @var QuestionNormalizer
     */
    private $questionNormalizer;

    /**
     * @var AnswerNormalizer
     */
    private $answerNormalizer;

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

    /**
     * @inheritdoc
     * @param AbstractAnsweredEvent $answeredEvent
     */
    public function normalize($answeredEvent, $format = null, array $context = []): array
    {
        return [
            'id' => $answeredEvent->getId()->toString(),
            'question' => $this->questionNormalizer->normalize($answeredEvent->getQuestion()),
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
