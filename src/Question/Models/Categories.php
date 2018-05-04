<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Models;

class Categories implements \IteratorAggregate
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
    public function getIterator()
    {
        return new \ArrayIterator($this->categories);
    }
}
