<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use VSV\GVQ_API\Quiz\Events\AnsweredIncorrect;

class AnsweredIncorrectNormalizer extends AbstractAnsweredEventNormalizer
{
    /**
     * @inheritdoc
     * @param AnsweredIncorrect $answeredIncorrect
     */
    public function normalize($answeredIncorrect, $format = null, array $context = []): array
    {
        return [
            'id' => $answeredIncorrect->getId()->toString(),
            'question' => $this->questionNormalizer->normalize($answeredIncorrect->getQuestion()),
            'answer' => $this->answerNormalizer->normalize($answeredIncorrect->getAnswer()),
            'answeredOn' => $answeredIncorrect->getAnsweredOn()->format(DATE_ATOM),
            'answeredTooLate' => $answeredIncorrect->isAnsweredTooLate(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof AnsweredIncorrect) && ($format === 'json');
    }
}
