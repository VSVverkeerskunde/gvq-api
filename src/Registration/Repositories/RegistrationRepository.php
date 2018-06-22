<?php declare(strict_types=1);

namespace VSV\GVQ_API\Registration\Repositories;

use VSV\GVQ_API\Registration\Models\Registration;
use VSV\GVQ_API\Registration\ValueObjects\UrlSuffix;

interface RegistrationRepository
{
    /**
     * @param Registration $registration
     */
    public function save(Registration $registration): void;

    /**
     * @param UrlSuffix $urlSuffix
     * @return Registration|null
     */
    public function getByUrlSuffix(UrlSuffix $urlSuffix): ?Registration;
}
