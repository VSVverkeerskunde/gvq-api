<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Statistics\Models\TopScore;
use VSV\GVQ_API\Statistics\Models\TopScores;

class TopScoresNormalizer implements NormalizerInterface
{
    /**
     * @var TopScoreNormalizer
     */
    private $topScoreNormalizer;

    /**
     * @param TopScoreNormalizer $topScoreNormalizer
     */
    public function __construct(TopScoreNormalizer $topScoreNormalizer)
    {
        $this->topScoreNormalizer = $topScoreNormalizer;
    }

    /**
     * @inheritdoc
     * @param TopScores $topScores
     */
    public function normalize($topScores, $format = null, array $context = []): array
    {
        return array_map(
            function (TopScore $topScore) use ($format, $context) {
                return $this->topScoreNormalizer->normalize($topScore, $format, $context);
            },
            $topScores->toArray()
        );
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof TopScores) && ($format === 'json' || $format === 'csv');
    }
}
