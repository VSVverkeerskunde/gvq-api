<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Question\Serializers\QuestionNormalizer;
use VSV\GVQ_API\Quiz\ValueObjects\QuestionResult;

class QuestionResultNormalizer implements NormalizerInterface
{
    /**
     * @var QuestionNormalizer
     */
    private $questionNormalizer;

    /**
     * @param QuestionNormalizer $questionNormalizer
     */
    public function __construct(QuestionNormalizer $questionNormalizer)
    {
        $this->questionNormalizer = $questionNormalizer;
    }

    /**
     * @inheritdoc
     * @param QuestionResult $questionResult
     * @throws \Exception
     */
    public function normalize($questionResult, $format = null, array $context = [])
    {
        return [
            'question' => $this->questionNormalizer->normalize(
                $questionResult->getQuestion(),
                'json',
                $context
            ),
            'answeredTooLate' => $questionResult->isAnsweredTooLate(),
            'score' => $questionResult->getScore(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof QuestionResult) && ($format === 'json');
    }
}
