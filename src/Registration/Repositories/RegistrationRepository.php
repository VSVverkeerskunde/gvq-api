<?php declare(strict_types=1);

namespace VSV\GVQ_API\Registration\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Registration\Models\Registration;
use VSV\GVQ_API\Registration\Repositories\Entities\RegistrationEntity;

interface RegistrationRepository
{
    /**
     * @param Registration $registration
     */
    public function save(Registration $registration): void;

    /**
     * @param string $urlSuffix
     * @return Registration|null
     */
    public function getByUrlSuffix(string $urlSuffix): ?Registration;

    /**
     * @param UuidInterface $userId
     * @return null|Registration
     */
    public function getByUserId(UuidInterface $userId): ?Registration;

    /**
     * @param UuidInterface $registrationId
     */
    public function delete(UuidInterface $registrationId): void;
}
