<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Contest\ValueObjects\ContestParticipant;

class ContestParticipantNormalizer implements NormalizerInterface
{
    /**
     * @inheritdoc
     * @param ContestParticipant $contestParticipant
     */
    public function normalize($contestParticipant, $format = null, array $context = array()): array
    {
        return [
            'email' => $contestParticipant->getEmail()->toNative(),
            'firstName' => $contestParticipant->getFirstName()->toNative(),
            'lastName' => $contestParticipant->getLastName()->toNative(),
            'dateOfBirth' => $contestParticipant->getDateOfBirth() ? $contestParticipant->getDateOfBirth()->format(DATE_ATOM) : null,
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof ContestParticipant) && ($format === 'json' || $format === 'csv');
    }
}
