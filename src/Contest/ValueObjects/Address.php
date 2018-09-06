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

    /**
     * @param NotEmptyString $street
     * @param NotEmptyString $number
     * @param NotEmptyString $postalCode
     * @param NotEmptyString $town
     */
    public function __construct(
        NotEmptyString $street,
        NotEmptyString $number,
        NotEmptyString $postalCode,
        NotEmptyString $town
    ) {
        $this->street = $street;
        $this->number = $number;
        $this->postalCode = $postalCode;
        $this->town = $town;
    }

    /**
     * @return NotEmptyString
     */
    public function getStreet(): NotEmptyString
    {
        return $this->street;
    }

    /**
     * @return NotEmptyString
     */
    public function getNumber(): NotEmptyString
    {
        return $this->number;
    }

    /**
     * @return NotEmptyString
     */
    public function getPostalCode(): NotEmptyString
    {
        return $this->postalCode;
    }

    /**
     * @return NotEmptyString
     */
    public function getTown(): NotEmptyString
    {
        return $this->town;
    }
}
