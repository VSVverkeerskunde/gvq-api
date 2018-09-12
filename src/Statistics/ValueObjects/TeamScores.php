<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\ValueObjects;

use VSV\GVQ_API\Common\ValueObjects\Collection;

class TeamScores implements Collection
{
    /**
     * @var TeamScore[]
     */
    private $teamScores;

    /**
     * @param TeamScore ...$teamScores
     */
    public function __construct(TeamScore ...$teamScores)
    {
        $this->teamScores = $teamScores;
    }

    /**
     * @inheritdoc
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->teamScores);
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        return count($this->teamScores);
    }

    /**
     * @return TeamScore[]
     */
    public function toArray(): array
    {
        return $this->teamScores;
    }

    /**
     * @return TeamScores
     */
    public function sortByParticipationCount(): TeamScores
    {
        usort(
            $this->teamScores,
            function (TeamScore $ts1, TeamScore $ts2): int {
                if ($ts1->getParticipationCount()->toNative() > $ts2->getParticipationCount()->toNative()) {
                    return -1;
                } elseif ($ts1->getParticipationCount()->toNative() < $ts2->getParticipationCount()->toNative()) {
                    return 1;
                } else {
                    return $this->sortAlphabetically($ts1, $ts2);
                }
            }
        );

        return $this;
    }

    /**
     * @return TeamScores
     */
    public function sortByRankingScore(): TeamScores
    {
        usort(
            $this->teamScores,
            function (TeamScore $ts1, TeamScore $ts2): int {
                if ($ts1->getRankingScore()->toNative() > $ts2->getRankingScore()->toNative()) {
                    return -1;
                } elseif ($ts1->getRankingScore()->toNative() < $ts2->getRankingScore()->toNative()) {
                    return 1;
                } else {
                    return $this->sortAlphabetically($ts1, $ts2);
                }
            }
        );

        return $this;
    }

    /**
     * @param TeamScore $ts1
     * @param TeamScore $ts2
     * @return int
     */
    private function sortAlphabetically(TeamScore $ts1, TeamScore $ts2): int
    {
        return strcmp(
            $ts1->getTeam()->getName()->toNative(),
            $ts2->getTeam()->getName()->toNative()
        );
    }
}
