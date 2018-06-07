<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Models;

use VSV\GVQ_API\Common\ValueObjects\Collection;

class Categories implements Collection
{
    /**
     * @var Category[]
     */
    private $categories;

    /**
     * @param Category ...$categories
     */
    public function __construct(Category ...$categories)
    {
        $this->categories = $categories;
    }

    /**
     * @inheritdoc
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->categories);
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        return count($this->categories);
    }

    /**
     * @return Category[]
     */
    public function toArray(): array
    {
        return $this->categories;
    }
}
