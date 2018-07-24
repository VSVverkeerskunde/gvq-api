<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Models;

use VSV\GVQ_API\Common\ValueObjects\Collection;

class Questions implements Collection
{
    /**
     * @var Question[]
     */
    private $questions;

    /**
     * Questions constructor.
     * @param Question ...$questions
     */
    public function __construct(Question ...$questions)
    {
        $this->questions = $questions;
    }

    /**
     * @inheritdoc
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->questions);
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        return count($this->questions);
    }

    /**
     * @return Question[]
     */
    public function toArray(): array
    {
        return $this->questions;
    }

    /**
     * Short questions based on the creation date.
     * The newest question will have index 0.
     */
    public function sortByNewest(): Questions
    {
        usort(
            $this->questions,
            function (Question $q1, Question $q2) {
                if ($q1->getCreatedOn() == $q2->getCreatedOn()) {
                    return 0;
                }

                return $q1->getCreatedOn() > $q2->getCreatedOn() ? -1 : 1;
            }
        );

        return $this;
    }
}
