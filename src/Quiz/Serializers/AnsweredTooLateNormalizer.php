<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Question\Serializers\QuestionNormalizer;
use VSV\GVQ_API\Quiz\Events\AnsweredTooLate;

class AnsweredTooLateNormalizer implements NormalizerInterface
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
     * @param AnsweredTooLate $answeredTooLate
     * @throws \Exception
     */
    public function normalize($answeredTooLate, $format = null, array $context = []): array
    {
        return [
            'id' => $answeredTooLate->getId()->toString(),
            'question' => $this->questionNormalizer->normalize($answeredTooLate->getQuestion()),
            'answeredOn' => $answeredTooLate->getAnsweredOn()->format(DATE_ATOM),
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof AnsweredTooLate) && ($format === 'json');
    }
}
