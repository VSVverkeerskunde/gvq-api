<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\User\Models\User;

class UserNormalizer implements NormalizerInterface
{
    /**
     * @inheritdoc
     * @param User $user
     */
    public function normalize($user, $format = null, array $context = array())
    {
        return [
            'id' => $user->getId()->toString(),
            'email' => $user->getEmail()->toNative(),
            'firstName' => $user->getFirstName()->toNative(),
            'lastName' => $user->getLastName()->toNative(),
            'role' => $user->getRole()->toNative(),
            'language' => $user->getLanguage()->toNative(),
            'active' => $user->isActive()
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof User) && ($format === 'json' || $format === 'csv');
    }
}
