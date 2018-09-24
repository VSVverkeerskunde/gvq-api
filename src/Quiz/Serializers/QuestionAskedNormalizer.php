<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Question\Serializers\QuestionNormalizer;
use VSV\GVQ_API\Quiz\Events\QuestionAsked;

class QuestionAskedNormalizer implements NormalizerInterface
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
     * @param QuestionAsked $questionAsked
     * @throws \Exception
     */
    public function normalize($questionAsked, $format = null, array $context = [])
    {
        return [
            'id' => $questionAsked->getId()->toString(),
            'question' => $this->questionNormalizer->normalize($questionAsked->getQuestion()),
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
