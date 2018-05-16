<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Question\Models\Answer;

class AnswerNormalizer implements NormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function normalize($answer, $format = null, array $context = []): array
    {
        /** @var Answer $answer */
        return [
            'id' => $answer->getId()->toString(),
            'text' => $answer->getText()->toNative(),
            'correct' => $answer->isCorrect(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof Answer) && ($format === 'json');
    }
}
