<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Repositories\Entities;

use Doctrine\ORM\Mapping as ORM;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Contest\ValueObjects\Address;

/**
 * @ORM\Embeddable()
 */
class AddressEmbeddable
{
    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $street;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $number;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $postalCode;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $town;

    /**
     * @param string $street
     * @param string $number
     * @param string $postalCode
     * @param string $town
     */
    private function __construct(
        string $street,
        string $number,
        string $postalCode,
        string $town
    ) {
        $this->street = $street;
        $this->number = $number;
        $this->postalCode = $postalCode;
        $this->town = $town;
    }

    /**
     * @param Address $address
     * @return AddressEmbeddable
     */
    public static function fromAddress(Address $address): AddressEmbeddable
    {
        return new AddressEmbeddable(
            $address->getStreet()->toNative(),
            $address->getNumber()->toNative(),
            $address->getPostalCode()->toNative(),
            $address->getTown()->toNative()
        );
    }

    /**
     * @return Address
     */
    public function toAddress(): Address
    {
        return new Address(
            new NotEmptyString($this->street),
            new NotEmptyString($this->number),
            new NotEmptyString($this->postalCode),
            new NotEmptyString($this->town)
        );
    }
}
