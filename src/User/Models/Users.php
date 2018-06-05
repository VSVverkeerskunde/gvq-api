<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Models;

class Users implements \IteratorAggregate
{
    /**
     * @var User[]
     */
    private $users;

    /**
     * @param User[] $users
     */
    public function __construct(User ...$users)
    {
        $this->users = $users;
    }

    /**
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->users);
    }
}
