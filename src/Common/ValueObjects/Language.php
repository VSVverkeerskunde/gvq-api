<?php declare(strict_types=1);

namespace VSV\GVQ_API\Common\ValueObjects;

class Language extends Enumeration
{
    const NL = 'nl';
    const FR = 'fr';

    /**
     * @inheritdoc
     */
    public function getAllowedValues(): array
    {
        return [
            self::NL,
            self::FR,
        ];
    }
}
