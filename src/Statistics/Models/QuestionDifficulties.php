<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Models;

use VSV\GVQ_API\Common\ValueObjects\Collection;

class QuestionDifficulties implements Collection
{
    /**
     * @var QuestionDifficulty[]
     */
    private $questionDifficulties;

    /**
     * @param QuestionDifficulty ...$questionDifficulties
     */
    public function __construct(QuestionDifficulty ...$questionDifficulties)
    {
        $this->questionDifficulties = $questionDifficulties;
    }

    /**
     * @inheritdoc
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->questionDifficulties);
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        return count($this->questionDifficulties);
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        return $this->questionDifficulties;
    }
}
