<?php declare(strict_types=1);

namespace VSV\GVQ_API\Partner\Models;

use VSV\GVQ_API\Common\ValueObjects\Collection;

class Partners implements Collection
{
    /**
     * @var Partner[]
     */
    private $partners;

    /**
     * @param Partner... $partners
     */
    public function __construct(Partner ...$partners)
    {
        $this->partners = $partners;
    }

    /**
     * @inheritdoc
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->partners);
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        return count($this->partners);
    }

    /**
     * @return Partner[]
     */
    public function toArray(): array
    {
        return $this->partners;
    }
}
