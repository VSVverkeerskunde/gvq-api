<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Serializers;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use VSV\GVQ_API\Question\Models\Answer;
use VSV\GVQ_API\Question\ValueObjects\NotEmptyString;

class AnswerDenormalizer implements DenormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = []): Answer
    {
        return new Answer(
            Uuid::fromString($data['id']),
            new NotEmptyString($data['text']),
            (bool) $data['correct']
        );
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return ($type === Answer::class) && ($format === 'json');
    }
}
