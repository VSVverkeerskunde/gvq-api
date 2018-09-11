<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Statistics\ValueObjects\TeamScore;
use VSV\GVQ_API\Team\Serializers\TeamNormalizer;

class TeamScoreNormalizer implements NormalizerInterface
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
     * @param TeamScore $teamScore
     */
    public function normalize($teamScore, $format = null, array $context = [])
    {
        $team = $this->teamNormalizer->normalize(
            $teamScore->getTeam(),
            $format
        );

        return [
            'team' => $team,
            'totalScore' => $teamScore->getTotalScore()->toNative(),
            'participationCount' => $teamScore->getParticipationCount()->toNative(),
            'rankingScore' => $teamScore->getRankingScore()->toNative(),
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return ($data instanceof TeamScore) && ($format === 'json');
    }
}
