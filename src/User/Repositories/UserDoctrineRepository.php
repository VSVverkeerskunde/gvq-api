<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Repositories;

use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;
use VSV\GVQ_API\User\Models\User;
use VSV\GVQ_API\User\Repositories\Entities\UserEntity;
use VSV\GVQ_API\User\ValueObjects\Email;

class UserDoctrineRepository extends AbstractDoctrineRepository implements UserRepository
{
    /**
     * @inheritdoc
     */
    protected function getRepositoryName(): string
    {
        return UserEntity::class;
    }

    /**
     * @param User $user
     */
    public function save(User $user): void
    {
        $userEntity = UserEntity::fromUser($user);

        $this->entityManager->persist($userEntity);
        $this->entityManager->flush();
    }

    /**
     * @param Email $email
     * @return null|User
     */
    public function getByEmail(Email $email): ?User
    {
        /** @var UserEntity|null $userEntity */
        $userEntity = $this->objectRepository->findOneBy(
            [
                'email' => $email->toNative(),
            ]
        );

        return $userEntity ? $userEntity->toUser() : null;
    }
}
