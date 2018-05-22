<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Serializers;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\User\Models\User;
use VSV\GVQ_API\User\ValueObjects\Email;
use VSV\GVQ_API\User\ValueObjects\Password;
use VSV\GVQ_API\User\ValueObjects\Role;

class UserDenormalizer implements DenormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = array()): User
    {
        return new User(
            Uuid::fromString($data['id']),
            new Email($data['email']),
            Password::fromHash($data['password']),
            new NotEmptyString($data['lastName']),
            new NotEmptyString($data['firstName']),
            new Role($data['role'])
        );
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return ($type === User::class) && ($format === 'json');
    }
}
