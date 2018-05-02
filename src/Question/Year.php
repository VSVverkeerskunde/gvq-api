<?php

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
        if ($value < 2018 || $value > 2100) {
            throw new \InvalidArgumentException(
                'Invalid value '.$value.' for year, value has to be above 2018 and below 2100'
            );
        }

        $this->value = $value;
    }
}
