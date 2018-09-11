<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Statistics\ValueObjects\TeamScore;
use VSV\GVQ_API\Statistics\ValueObjects\TeamScores;

class TeamScoresNormalizer implements NormalizerInterface
{
    /**
     * @var TeamScoreNormalizer
     */
    private $teamScoreNormalizer;

    /**
     * @param TeamScoreNormalizer $teamScoreNormalizer
     */
    public function __construct(TeamScoreNormalizer $teamScoreNormalizer)
    {
        $this->teamScoreNormalizer = $teamScoreNormalizer;
    }

    /**
     * @inheritdoc
     * @param TeamScores $teamScores
     */
    public function normalize($teamScores, $format = null, array $context = [])
    {
        return array_map(
            function (TeamScore $teamScore) use ($format, $context) {
                return $this->teamScoreNormalizer->normalize($teamScore, $format, $context);
            },
            $teamScores->toArray()
        );
    }

    public function supportsNormalization($data, $format = null)
    {
        return ($data instanceof TeamScores) && ($format === 'json');
    }
}
