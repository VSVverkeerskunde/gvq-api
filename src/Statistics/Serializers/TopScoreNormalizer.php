<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Statistics\Models\TopScore;

class TopScoreNormalizer implements NormalizerInterface
{
    /**
     * @inheritdoc
     * @param TopScore $topScore
     */
    public function normalize($topScore, $format = null, array $context = array()): array
    {
        return [
            'email' => $topScore->getEmail()->toNative(),
            'score' => $topScore->getScore()->toNative(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof TopScore) && ($format === 'json' || $format === 'csv');
    }
}
