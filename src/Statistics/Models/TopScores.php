<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Models;

use VSV\GVQ_API\Common\ValueObjects\Collection;

class TopScores implements Collection
{
    /**
     * @var TopScore[]
     */
    private $topScores;

    /**
     * TopScores constructor.
     * @param TopScore ...$topScores
     */
    public function __construct(TopScore ...$topScores)
    {
        $this->topScores = $topScores;

        usort(
            $this->topScores,
            function (TopScore $t1, TopScore $t2) {
                if ($t1->getScore()->toNative() === $t2->getScore()->toNative()) {
                    if ($t1->getEmail()->toNative() === $t2->getEmail()->toNative()) {
                        return 0;
                    }

                    return $t1->getEmail()->toNative() > $t2->getEmail()->toNative();
                }

                return $t1->getScore()->toNative() <  $t2->getScore()->toNative();
            }
        );
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->topScores);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->topScores);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->topScores;
    }
}
