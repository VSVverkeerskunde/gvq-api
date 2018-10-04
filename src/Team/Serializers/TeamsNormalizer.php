<?php declare(strict_types=1);

namespace VSV\GVQ_API\Team\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Team\Models\Team;
use VSV\GVQ_API\Team\Models\Teams;

class TeamsNormalizer implements NormalizerInterface
{
    /**
     * @var TeamNormalizer
     */
    private $teamNormalizer;

    /**
     * @param TeamNormalizer $teamNormalizer
     */
    public function __construct(TeamNormalizer $teamNormalizer)
    {
        $this->teamNormalizer = $teamNormalizer;
    }

    /**
     * @inheritdoc
     * @param Teams $teams
     */
    public function normalize($teams, $format = null, array $context = []): array
    {
        return array_map(
            function (Team $team) use ($format, $context) {
                return $this->teamNormalizer->normalize($team, $format, $context);
            },
            $teams->toArray()
        );
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof Teams) && ($format === 'json');
    }
}
