<?php

namespace VSV\GVQ_API\Statistics\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Statistics\Models\RankedCompanyParticipant;

class RankedCompanyParticipantNormalizer implements NormalizerInterface
{
    /**
     * @inheritdoc
     * @param \VSV\GVQ_API\Statistics\Models\RankedCompanyParticipant $rankedCompanyParticipant
     */
    public function normalize($rankedCompanyParticipant, $format = null, array $context = [])
    {
        return [
            'email' => $rankedCompanyParticipant->getEmail()->toNative(),
            'score' => $rankedCompanyParticipant->getScore()->toNative(),
            'answer1' => $rankedCompanyParticipant->getAnswer1() ? $rankedCompanyParticipant->getAnswer1()->toNative() : '',
            'answer2' => $rankedCompanyParticipant->getAnswer2() ? $rankedCompanyParticipant->getAnswer2()->toNative() : '',
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return ($data instanceof RankedCompanyParticipant) && ($format === 'csv');
    }
}