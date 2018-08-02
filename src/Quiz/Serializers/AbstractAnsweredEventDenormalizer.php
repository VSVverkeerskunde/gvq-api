<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use VSV\GVQ_API\Question\Serializers\AnswerDenormalizer;
use VSV\GVQ_API\Question\Serializers\QuestionDenormalizer;
use VSV\GVQ_API\Quiz\Events\AbstractAnsweredEvent;

abstract class AbstractAnsweredEventDenormalizer implements DenormalizerInterface
{
    /**
     * @var QuestionDenormalizer
     */
    private $questionDenormalizer;

    /**
     * @var AnswerDenormalizer
     */
    private $answerDenormalizer;

    /**
     * @param QuestionDenormalizer $questionDenormalizer
     * @param AnswerDenormalizer $answerDenormalizer
     */
    public function __construct(QuestionDenormalizer $questionDenormalizer, AnswerDenormalizer $answerDenormalizer)
    {
        $this->questionDenormalizer = $questionDenormalizer;
        $this->answerDenormalizer = $answerDenormalizer;
    }


    public function denormalize($data, $class, $format = null, array $context = [])
    {
        
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        $class = $this->getDenormalizerName();

        return ($type === $class) && ($format === 'json');
    }


    /**
     * @return string
     */
    abstract protected function getDenormalizerName(): string;
}
