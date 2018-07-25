<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\ValueObjects;

use VSV\GVQ_API\Common\ValueObjects\Enumeration;

class Role extends Enumeration
{
    const ADMIN = 'admin';
    const VSV = 'vsv';
    const CONTACT = 'contact';

    /**
     * @inheritdoc
     */
    public function getAllowedValues(): array
    {
        return [
            self::ADMIN,
            self::VSV,
            self::CONTACT,
        ];
    }
}
