<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\ValueObjects;

class Roles implements \IteratorAggregate, \Countable
{
    /**
     * @var Role[]
     */
    private $roles;

    /**
     * @param Role ...$roles
     */
    public function __construct(Role ...$roles)
    {
        $this->roles = $roles;
    }

    /**
     * @inheritdoc
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->roles);
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        return count($this->roles);
    }

    /**
     * @return Role[]
     */
    public function toArray(): array
    {
        return $this->roles;
    }
}
