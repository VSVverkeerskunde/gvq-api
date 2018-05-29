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
            'lastName' => $user->getLastName()->toNative(),
            'firstName' => $user->getFirstName()->toNative(),
            'role' => $user->getRole()->toNative(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof User) && ($format === 'json');
    }
}
