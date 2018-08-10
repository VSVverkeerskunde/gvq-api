<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Question\Models\Answer;

class AnswerNormalizer implements NormalizerInterface
{
    /**
     * @inheritdoc
     * @param Answer $answer
     */
    public function normalize($answer, $format = null, array $context = []): array
    {
        $questionAsked = !empty($context['questionAsked']) && $context['questionAsked'] === true;

        return [
            'id' => $answer->getId()->toString(),
            'index' => $answer->getIndex()->toNative(),
            'text' => $answer->getText()->toNative(),
            'correct' => $questionAsked ? null : $answer->isCorrect(),
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
