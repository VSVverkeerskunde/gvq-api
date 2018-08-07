<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Quiz\Events\QuizFinished;

class QuizFinishedNormalizer implements NormalizerInterface
{
    /**
     * @inheritdoc
     * @param QuizFinished $quizFinished
     */
    public function normalize($quizFinished, $format = null, array $context = array())
    {
        return [
            'id' => $quizFinished->getId()->toString(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof QuizFinished) && ($format === 'json');
    }
}
