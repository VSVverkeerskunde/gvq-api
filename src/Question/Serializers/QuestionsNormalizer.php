<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Models\Questions;

class QuestionsNormalizer implements NormalizerInterface
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
     * @param Questions $questions
     */
    public function normalize($questions, $format = null, array $context = []): array
    {
        return array_map(
            function (Question $question) use ($format, $context) {
                return $this->questionNormalizer->normalize($question, $format, $context);
            },
            $questions->toArray()
        );
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof Questions) && ($format === 'json');
    }
}
