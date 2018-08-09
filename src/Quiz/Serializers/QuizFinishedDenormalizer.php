<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use VSV\GVQ_API\Quiz\Events\QuizFinished;

class QuizFinishedDenormalizer implements DenormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = []): QuizFinished
    {
        return new QuizFinished(
            Uuid::fromString($data['id']),
            $data['score']
        );
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return ($type === QuizFinished::class) && ($format === 'json');
    }
}
