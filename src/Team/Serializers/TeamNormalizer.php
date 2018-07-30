<?php declare(strict_types=1);

namespace VSV\GVQ_API\Team\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Team\Models\Team;

class TeamNormalizer implements NormalizerInterface
{
    /**
     * @inheritdoc
     * @param Team $team
     */
    public function normalize($team, $format = null, array $context = [])
    {
        return [
            'id' => $team->getId()->toString(),
            'name' => $team->getName()->toNative(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof Team) && ($format === 'json');
    }
}
