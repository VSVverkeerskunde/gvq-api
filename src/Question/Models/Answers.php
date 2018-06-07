<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Models;

use VSV\GVQ_API\Common\ValueObjects\Collection;

class Answers implements Collection
{
    /**
     * @var Answer[]
     */
    private $answers;

    /**
     * @param Answer ...$answers
     */
    public function __construct(Answer ...$answers)
    {
        $this->answers = $answers;
    }

    /**
     * @inheritdoc
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->answers);
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        return count($this->answers);
    }

    /**
     * @return Answer[]
     */
    public function toArray(): array
    {
        return $this->answers;
    }
}
