<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Question\Serializers\QuestionNormalizer;
use VSV\GVQ_API\Quiz\Events\QuestionAsked;

class QuestionAskedNormalizer implements NormalizerInterface
{
    /**
     * @var QuestionResultNormalizer
     */
    private $questionResultNormalizer;

    /**
     * @param QuestionResultNormalizer $questionResultNormalizer
     */
    public function __construct(QuestionResultNormalizer $questionResultNormalizer)
    {
        $this->questionResultNormalizer = $questionResultNormalizer;
    }

    /**
     * @inheritdoc
     * @param QuestionAsked $questionAsked
     */
    public function normalize($questionAsked, $format = null, array $context = [])
    {
        return [
            'id' => $questionAsked->getId()->toString(),
            'questionResult' => $this->questionResultNormalizer->normalize($questionAsked->getQuestionResult()),
            'askedOn' => $questionAsked->getAskedOn()->format(DATE_ATOM),
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof QuestionAsked) && ($format === 'json');
    }
}
