<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question;

class Year
{
    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value
     */
    public function __construct(int $value)
    {
        if ($value <= 2018 || $value >= 2099) {
            throw new \InvalidArgumentException(
                'Invalid value '.$value.' for year, value has to be above 2018 and below 2100'
            );
        }

        $this->value = $value;
    }

    /**
     * @return int
     */
    public function toNative(): int
    {
        return $this->value;
    }

    /**
     * @param Year $year
     * @return bool
     */
    public function equals(Year $year): bool
    {
        return $this->toNative() === $year->toNative();
    }
}
