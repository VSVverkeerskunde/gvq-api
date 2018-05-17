<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Models;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\ValueObjects\Alias;

class Company
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var NotEmptyString
     */
    private $name;

    /**
     * @var Alias[]
     */
    private $aliases;

    /**
     * @param UuidInterface $id
     * @param NotEmptyString $name
     * @param Alias[] $aliases
     */
    public function __construct(UuidInterface $id, NotEmptyString $name, array $aliases)
    {
        $this->id = $id;
        $this->name = $name;
        $this->aliases = $aliases;
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return NotEmptyString
     */
    public function getName(): NotEmptyString
    {
        return $this->name;
    }

    /**
     * @return Alias[]
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }
}
