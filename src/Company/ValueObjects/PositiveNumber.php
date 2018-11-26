<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\ValueObjects;

use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;

class PositiveNumber extends NaturalNumber
{
    /**
     * @param int $value
     */
    public function __construct(int $value)
    {
        if ($value < 1) {
            throw new \InvalidArgumentException(
                'Value has to be greater than 0, '.$value.' given.'
            );
        }

        parent::__construct($value);
    }
}
