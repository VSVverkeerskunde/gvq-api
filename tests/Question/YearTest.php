<?php

namespace VSV\GVQ_API\Question;

use PHPUnit\Framework\TestCase;

class YearTest extends TestCase
{
    /**
     * @test
     */
    public function it_only_accepts_supported_values()
    {
        $year = new Year(2050);
        $this->assertNotNull($year);
    }

    /**
     * @test
     */
    public function it_throws_for_unsupported_values()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid value 2101 for year, value has to be above 2018 and below 2100');
        new Year(2101);
    }
}
