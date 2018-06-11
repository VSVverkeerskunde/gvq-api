<?php declare(strict_types=1);

namespace VSV\GVQ_API\Registration\Repositories;

use VSV\GVQ_API\Registration\Models\Registration;

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
}
