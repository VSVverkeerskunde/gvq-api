<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Contest\Models\ContestParticipation;
use VSV\GVQ_API\Contest\Models\ContestParticipations;

class ContestParticipationsNormalizer implements NormalizerInterface
{
    /**
     * @var ContestParticipationNormalizer
     */
    private $contestParticipationNormalizer;

    /**
     * @param ContestParticipationNormalizer $contestParticipationNormalizer
     */
    public function __construct(
        ContestParticipationNormalizer $contestParticipationNormalizer
    ) {
        $this->contestParticipationNormalizer = $contestParticipationNormalizer;
    }

    /**
     * @inheritdoc
     * @param ContestParticipations $contestParticipations
     */
    public function normalize($contestParticipations, $format = null, array $context = array()): array
    {
        return array_map(
            function (ContestParticipation $contestParticipation) use ($format, $context) {
                return $this->contestParticipationNormalizer->normalize(
                    $contestParticipation,
                    $format,
                    $context
                );
            },
            $contestParticipations->toArray()
        );
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof ContestParticipations) && ($format === 'json' || $format === 'csv');
    }
}
