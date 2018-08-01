<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use VSV\GVQ_API\Quiz\Events\QuizStarted;

class QuizStartedDenormalizer implements DenormalizerInterface
{
    /**
     * @var QuizDenormalizer
     */
    private $quizDenormalizer;

    /**
     * @param QuizDenormalizer $quizDenormalizer
     */
    public function __construct(QuizDenormalizer $quizDenormalizer)
    {
        $this->quizDenormalizer = $quizDenormalizer;
    }

    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = array()): QuizStarted
    {
        return new QuizStarted(
            Uuid::fromString($data['id']),
            $this->quizDenormalizer->denormalize(
                $data['quiz'],
                QuizStarted::class,
                $format,
                $context
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return ($type === QuizStarted::class) && ($format === 'json');
    }
}
