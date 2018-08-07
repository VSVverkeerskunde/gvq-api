<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\ValueObjects;

use VSV\GVQ_API\Common\ValueObjects\Enumeration;

class QuizChannel extends Enumeration
{
    const INDIVIDUAL = 'individual';
    const COMPANY = 'company';
    const PARTNER = 'partner';
    const CUP = 'cup';

    /**
     * @inheritdoc
     */
    public function getAllowedValues(): array
    {
        return [
            self::INDIVIDUAL,
            self::COMPANY,
            self::PARTNER,
            self::CUP,
        ];
    }
}
