<?php declare(strict_types=1);

namespace VSV\GVQ_API\Registration\Repositories;

use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;
use VSV\GVQ_API\Registration\Models\Registration;
use VSV\GVQ_API\Registration\Repositories\Entities\RegistrationEntity;
use VSV\GVQ_API\Registration\ValueObjects\UrlSuffix;

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
     * @param UrlSuffix $urlSuffix
     * @return Registration|null
     */
    public function getByUrlSuffix(UrlSuffix $urlSuffix): ?Registration
    {
        /** @var RegistrationEntity|null $registrationEntity */
        $registrationEntity = $this->objectRepository->findOneBy(
            [
                'urlSuffix' => $urlSuffix->toNative(),
            ]
        );

        return $registrationEntity ? $registrationEntity->toRegistration() : null;
    }
}
