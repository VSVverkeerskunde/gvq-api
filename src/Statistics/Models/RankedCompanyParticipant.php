<?php

namespace VSV\GVQ_API\Statistics\Models;

use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;
use VSV\GVQ_API\User\ValueObjects\Email;

class RankedCompanyParticipant
{
    /**
     * @var \VSV\GVQ_API\Company\ValueObjects\PositiveNumber|null
     */
    private $answer1;

    /**
     * @var \VSV\GVQ_API\Company\ValueObjects\PositiveNumber|null
     */
    private $answer2;

    /**
     * @var \VSV\GVQ_API\Statistics\Models\TopScore
     */
    private $topScore;

    public function __construct(TopScore $topScore, NaturalNumber $answer1 = null, NaturalNumber $answer2 = null)
    {
        $this->answer1 = $answer1;
        $this->answer2 = $answer2;
        $this->topScore = $topScore;
    }

    public function getScore(): NaturalNumber
    {
        return $this->topScore->getScore();
    }

    public function getEmail(): Email
    {
        return $this->topScore->getEmail();
    }

    public function compare(
        RankedCompanyParticipant $otherParticipant,
        NaturalNumber $answer1,
        NaturalNumber $answer2)
    {
        $scoreCmp = $this->compareNaturalNumber($this->getScore(), $otherParticipant->getScore());

        if ($scoreCmp !== 0) {
            return $scoreCmp;
        }

        $answer1Cmp = $this->compareDistanceToAnswer1($otherParticipant, $answer1);

        if ($answer1Cmp !== 0) {
            return $answer1Cmp;
        }

        $answer2Cmp = $this->compareDistanceToAnswer2($otherParticipant, $answer2);

        return $answer2Cmp;

    }

    private function compareNaturalNumber(NaturalNumber $n1 = null, NaturalNumber $n2 = null): int
    {
        if (null === $n1 && null === $n2) {
            return 0;
        }

        if (null === $n1) {
            return -1;
        }

        if (null === $n2) {
            return +1;
        }

        if ($n1->toNative() === $n2->toNative()) {
            return 0;
        }

        return ($n1->toNative() > $n2->toNative()) ? 1 : -1;
    }

    private function compareDistanceToAnswer1(RankedCompanyParticipant $otherParticipant, $answer1): int
    {
        return $this->compareDistance(
            $this->distanceToAnswer1($answer1),
            $otherParticipant->distanceToAnswer1($answer1)
        );
    }

    private function compareDistanceToAnswer2(RankedCompanyParticipant $otherParticipant, $answer2): int
    {
        return $this->compareDistance(
            $this->distanceToAnswer2($answer2),
            $otherParticipant->distanceToAnswer2($answer2)
        );
    }

    /**
     * Lower distances are considered higher!
     */
    private function compareDistance(NaturalNumber $d1 = null, NaturalNumber $d2 = null): int
    {
        if (null === $d1 && null === $d2) {
            return 0;
        }

        if (null === $d1) {
            return -1;
        }

        if (null === $d2) {
            return +1;
        }

        // We reverse the arguments here, as lower distances should be
        // considered better!
        return $this->compareNaturalNumber($d2, $d1);
    }

    private function distanceToAnswer1(NaturalNumber $answer1): ?NaturalNumber
    {
        return $this->distance($answer1, $this->answer1);
    }

    private function distance(NaturalNumber $a, NaturalNumber $b = null): ?NaturalNumber {
        if (null === $b) {
            return null;
        }

        return new NaturalNumber(
            abs(
                $a->toNative() - $b->toNative()
            )
        );
    }

    private function distanceToAnswer2(NaturalNumber $answer2): ?NaturalNumber
    {
        return $this->distance($answer2, $this->answer2);
    }

    /**
     * @return null|\VSV\GVQ_API\Company\ValueObjects\PositiveNumber
     */
    public function getAnswer1(
    ): ?\VSV\GVQ_API\Company\ValueObjects\PositiveNumber
    {
        return $this->answer1;
    }

    /**
     * @return null|\VSV\GVQ_API\Company\ValueObjects\PositiveNumber
     */
    public function getAnswer2(
    ): ?\VSV\GVQ_API\Company\ValueObjects\PositiveNumber
    {
        return $this->answer2;
    }
}
