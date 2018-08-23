<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\ValueObjects;

use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;

class Address
{
    /**
     * @var NotEmptyString
     */
    private $street;

    /**
     * @var NotEmptyString
     */
    private $number;

    /**
     * @var NotEmptyString
     */
    private $postalCode;

    /**
     * @var NotEmptyString
     */
    private $town;
}
