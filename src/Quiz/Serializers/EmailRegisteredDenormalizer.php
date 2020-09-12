<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Serializers\QuestionDenormalizer;
use VSV\GVQ_API\Quiz\Events\AnsweredTooLate;
use VSV\GVQ_API\Quiz\Events\EmailRegistered;
use VSV\GVQ_API\User\ValueObjects\Email;

class EmailRegisteredDenormalizer implements DenormalizerInterface
{
    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function denormalize($data, $class, $format = null, array $context = []): EmailRegistered
    {
        return new EmailRegistered(
            Uuid::fromString($data['id']),
            new Email(($data['email']))
        );
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return ($type === EmailRegistered::class) && ($format === 'json');
    }
}
