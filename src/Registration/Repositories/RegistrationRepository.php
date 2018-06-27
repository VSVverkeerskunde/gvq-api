<?php declare(strict_types=1);

namespace VSV\GVQ_API\Registration\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Registration\Models\Registration;
use VSV\GVQ_API\Registration\ValueObjects\UrlSuffix;

interface RegistrationRepository
{
    /**
     * @param Registration $registration
     */
    public function save(Registration $registration): void;

    /**
     * @param UuidInterface $id
     */
    public function delete(UuidInterface $id): void;

    /**
     * @param UuidInterface $id
     * @return null|Registration
     */
    public function getById(UuidInterface $id): ?Registration;

    /**
     * @param UrlSuffix $urlSuffix
     * @return Registration|null
     */
    public function getByUrlSuffix(UrlSuffix $urlSuffix): ?Registration;

    /**
     * @param UuidInterface $id
     * @return null|Registration
     */
    public function getByUserId(UuidInterface $id): ?Registration;
}
