<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Quiz\Events\QuizStarted;

class QuizStartedNormalizer implements NormalizerInterface
{
    /**
     * @var QuizNormalizer
     */
    private $quizNormalizer;

    /**
     * @param QuizNormalizer $quizNormalizer
     */
    public function __construct(QuizNormalizer $quizNormalizer)
    {
        $this->quizNormalizer = $quizNormalizer;
    }

    /**
     * @inheritdoc
     * @param QuizStarted $quizStarted
     */
    public function normalize($quizStarted, $format = null, array $context = array())
    {
        return [
            'id' => $quizStarted->getId()->toString(),
            'quiz' => $this->quizNormalizer->normalize($quizStarted->getQuiz())
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof QuizStarted) && ($format === 'json');
    }
}
