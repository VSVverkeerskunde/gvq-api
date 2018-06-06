<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Models;

class Users implements \IteratorAggregate
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
}
