<?php declare(strict_types=1);

namespace VSV\GVQ_API\Registration\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;
use VSV\GVQ_API\Registration\Models\Registration;
use VSV\GVQ_API\Registration\Repositories\Entities\RegistrationEntity;

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

        // The user object inside registration is not managed,
        // therefore we need to use merge instead of persist.
        // When user wouldn't exist yet, the user is not created.
        $this->entityManager->merge($registrationEntity);
        $this->entityManager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getByUrlSuffix(string $urlSuffix): ?Registration
    {
        /** @var RegistrationEntity|null $registrationEntity */
        $registrationEntity = $this->objectRepository->findOneBy(
            [
                'urlSuffix' => $urlSuffix,
            ]
        );

        return $registrationEntity ? $registrationEntity->toRegistration() : null;
    }

    /**
     * @inheritdoc
     */
    public function getByUserId(UuidInterface $userId): ?Registration
    {
        $registrationEntity = $this->objectRepository->findOneBy(
            [
                'userEntity' => $userId->toString(),
            ]
        );

        return $registrationEntity ? $registrationEntity->toRegistration() : null;
    }

    /**
     * @inheritdoc
     */
    public function delete(UuidInterface $registrationId): void
    {
        /** @var Registration $registrationEntity */
        $registrationEntity = $this->objectRepository->findOneBy(
          [
              'id' => $registrationId,
          ]
        );

        if ($registrationEntity !== null) {
            $this->entityManager->merge($registrationEntity);
            $this->entityManager->remove($registrationEntity);
            $this->entityManager->flush();
        }
    }
}
