<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Contest\ValueObjects\Address;

class AddressNormalizer implements NormalizerInterface
{
    /**
     * @inheritdoc
     * @param Address $address
     */
    public function normalize($address, $format = null, array $context = array()): array
    {
        return [
            'street' => $address->getStreet()->toNative(),
            'number' => $address->getNumber()->toNative(),
            'postal' => $address->getPostalCode()->toNative(),
            'town' => $address->getTown()->toNative(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof Address) && ($format === 'json' || $format === 'csv');
    }
}
