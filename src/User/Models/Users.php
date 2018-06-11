<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Models;

use VSV\GVQ_API\Common\ValueObjects\Collection;

class Users implements Collection
{
    /**
     * @var User[]
     */
    private $users;

    /**
     * @param User ...$users
     */
    public function __construct(User ...$users)
    {
        $this->users = $users;
    }

    /**
     * @inheritdoc
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->users);
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        return count($this->users);
    }

    /**
     * @return User[]
     */
    public function toArray(): array
    {
        return $this->users;
    }
}
