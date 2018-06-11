<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\User\Models\User;
use VSV\GVQ_API\User\Models\Users;

class UsersNormalizer implements NormalizerInterface
{
    /**
     * @var UserNormalizer
     */
    private $userNormalizer;

    /**
     * @param UserNormalizer $userNormalizer
     */
    public function __construct(UserNormalizer $userNormalizer)
    {
        $this->userNormalizer = $userNormalizer;
    }

    /**
     * @inheritdoc
     * @param Users $users
     */
    public function normalize($users, $format = null, array $context = []): array
    {
        return array_map(
            function (User $user) use ($format, $context) {
                return $this->userNormalizer->normalize($user, $format, $context);
            },
            $users->toArray()
        );
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof Users) && ($format === 'json' || $format === 'csv');
    }
}
