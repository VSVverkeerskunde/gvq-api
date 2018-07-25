<?php declare(strict_types=1);

namespace VSV\GVQ_API\Partner\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Partner\Models\Partner;

class PartnerNormalizer implements NormalizerInterface
{
    /**
     * @inheritdoc
     * @param Partner $partner
     */
    public function normalize($partner, $format = null, array $context = []): array
    {
        return [
            'id' => $partner->getId()->toString(),
            'name' => $partner->getName()->toNative(),
            'alias' => $partner->getAlias()->toNative(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof Partner) && ($format === 'json');
    }
}
