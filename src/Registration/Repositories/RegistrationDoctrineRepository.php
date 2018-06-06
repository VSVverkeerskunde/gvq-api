<?php declare(strict_types=1);

namespace VSV\GVQ_API\Registration\Repositories;

use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;
use VSV\GVQ_API\Registration\Models\Registration;
use VSV\GVQ_API\Registration\Repositories\Entities\RegistrationEntity;
use VSV\GVQ_API\User\Repositories\Entities\UserEntity;

class RegistrationDoctrineRepository extends AbstractDoctrineRepository implements RegistrationRepository
{
    /**
     * @inheritdoc
     */
    protected function getRepositoryName(): string
    {
        return RegistrationEntity::class;
    }

    /**
     * @inheritdoc
     */
    public function save(Registration $registration): void
    {
        $registrationEntity = RegistrationEntity::fromRegistration($registration);

        /** @var UserEntity $userEntity */
        $userEntity = $this->entityManager->find(
            UserEntity::class,
            $registrationEntity->getUserEntity()->getId()
        );

        if ($userEntity == null) {
            throw new \InvalidArgumentException(
                'User with id: '.$registrationEntity->getUserEntity()->getId().' not found.'
            );
        }

        $registrationEntity->setUserEntity($userEntity);

        $this->entityManager->persist($registrationEntity);
        $this->entityManager->flush();
    }

    /**
     * @param string $hashCode
     * @return Registration
     */
    public function getByHashCode(string $hashCode): ?Registration
    {
        /** @var RegistrationEntity|null $registrationEntity */
        $registrationEntity = $this->objectRepository->findOneBy(
            [
                'hashCode' => $hashCode,
            ]
        );

        return $registrationEntity ? $registrationEntity->toRegistration() : null;
    }
}
