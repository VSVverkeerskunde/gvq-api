<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;
use VSV\GVQ_API\User\Models\User;
use VSV\GVQ_API\User\Models\Users;
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
     * @inheritdoc
     */
    public function save(User $user): void
    {
        $userEntity = UserEntity::fromUser($user);

        $this->entityManager->persist($userEntity);
        $this->entityManager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getById(UuidInterface $id): ?User
    {
        /** @var UserEntity|null $userEntity */
        $userEntity = $this->objectRepository->findOneBy(
            [
                'id' => $id->toString(),
            ]
        );

        return $userEntity ? $userEntity->toUser() : null;
    }

    /**
     * @inheritdoc
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

    /**
     * @inheritdoc
     */
    public function getAll(): ?Users
    {
        /** @var UserEntity[] $userEntities */
        $userEntities = $this->objectRepository->findAll();

        if (empty($userEntities)) {
            return null;
        }

        return new Users(
            ...array_map(
                function (UserEntity $questionEntity) {
                    return $questionEntity->toUser();
                },
                $userEntities
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function update(User $user): void
    {
        $this->entityManager->merge(
            UserEntity::fromUser($user)
        );
        $this->entityManager->flush();
    }
}
