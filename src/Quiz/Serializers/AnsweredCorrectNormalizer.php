<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use VSV\GVQ_API\Quiz\Events\AnsweredCorrect;

class AnsweredCorrectNormalizer extends AbstractAnsweredEventNormalizer
{
    /**
     * @inheritdoc
     * @param AnsweredCorrect $answeredCorrect
     */
    public function normalize($answeredCorrect, $format = null, array $context = []): array
    {
        return [
            'id' => $answeredCorrect->getId()->toString(),
            'question' => $this->questionNormalizer->normalize($answeredCorrect->getQuestion()),
            'answer' => $this->answerNormalizer->normalize($answeredCorrect->getAnswer()),
            'answeredOn' => $answeredCorrect->getAnsweredOn()->format(DATE_ATOM),
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof AnsweredCorrect) && ($format === 'json');
    }
}
