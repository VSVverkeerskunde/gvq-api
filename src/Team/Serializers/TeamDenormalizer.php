<?php declare(strict_types=1);

namespace VSV\GVQ_API\Team\Serializers;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Team\Models\Team;

class TeamDenormalizer implements DenormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = []): Team
    {
        return new Team(
            Uuid::fromString($data['id']),
            new NotEmptyString($data['name'])
        );
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return ($type === Team::class) && ($format === 'json');
    }
}
