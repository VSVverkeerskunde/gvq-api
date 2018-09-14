<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Contest\Models\ContestParticipation;

class ContestParticipationNormalizer implements NormalizerInterface
{
    /**
     * @var ContestParticipantNormalizer
     */
    private $contestParticipantNormalizer;

    /**
     * @var AddressNormalizer
     */
    private $addressNormalizer;

    /**
     * @param ContestParticipantNormalizer $contestParticipantNormalizer
     * @param AddressNormalizer $addressNormalizer
     */
    public function __construct(
        ContestParticipantNormalizer $contestParticipantNormalizer,
        AddressNormalizer $addressNormalizer
    ) {
        $this->addressNormalizer = $addressNormalizer;
        $this->contestParticipantNormalizer = $contestParticipantNormalizer;
    }

    /**
     * @inheritdoc
     * @param ContestParticipation $contestParticipation
     */
    public function normalize($contestParticipation, $format = null, array $context = array()): array
    {
        return [
            'id' => $contestParticipation->getId()->toString(),
            'year' => $contestParticipation->getYear()->toNative(),
            'channel' => $contestParticipation->getChannel()->toNative(),
            'contestParticipant' => $this->contestParticipantNormalizer->normalize(
                $contestParticipation->getContestParticipant(),
                'json'
            ),
            'address' => $this->addressNormalizer->normalize(
                $contestParticipation->getAddress(),
                'json'
            ),
            'answer1' => $contestParticipation->getAnswer1()->toNative(),
            'answer2' => $contestParticipation->getAnswer2()->toNative(),
            'gdpr1' => $contestParticipation->isGdpr1(),
            'gdpr2' => $contestParticipation->isGdpr2(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof ContestParticipation) && ($format === 'json' || $format === 'csv');
    }
}
