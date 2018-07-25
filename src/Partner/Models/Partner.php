<?php declare(strict_types=1);

namespace VSV\GVQ_API\Partner\Models;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\ValueObjects\Alias;

class Partner
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
     * @var Alias
     */
    private $alias;

    /**
     * @param UuidInterface $id
     * @param NotEmptyString $name
     * @param Alias $alias
     */
    public function __construct(UuidInterface $id, NotEmptyString $name, Alias $alias)
    {
        $this->id = $id;
        $this->name = $name;
        $this->alias = $alias;
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
     * @return Alias
     */
    public function getAlias(): Alias
    {
        return $this->alias;
    }
}
