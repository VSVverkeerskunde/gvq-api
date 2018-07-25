<?php declare(strict_types=1);

namespace VSV\GVQ_API\Partner\Serializers;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\ValueObjects\Alias;
use VSV\GVQ_API\Partner\Models\Partner;

class PartnerDenormalizer implements DenormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = array()): Partner
    {
        return new Partner(
            Uuid::fromString($data['id']),
            new NotEmptyString($data['name']),
            new Alias($data['alias'])
        );
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return ($type === Partner::class) && ($format === 'json');
    }
}
