<?php

namespace VSV\GVQ_API\Question;

use PHPUnit\Framework\TestCase;

class YearTest extends TestCase
{
    /**
     * @var Year
     */
    private $year;

    /**
     * @var int
     */
    private $value = 2050;

    protected function setUp()
    {
        $this->year = new Year($this->value);
    }

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
        $this->expectExceptionMessage(
            'Invalid value 2101 for year, value has to be above 2018 and below 2100'
        );

        new Year(2101);
    }

    /**
     * @test
     */
    public function it_supports_to_native()
    {
        $this->assertSame($this->value, $this->year->toNative());
    }

    /**
     * @test
     */
    public function it_supports_to_string()
    {
        $this->assertSame((string)$this->value, $this->year->__toString());
    }

    /**
     * @test
     * @dataProvider yearsProvider
     * @param Year $year
     * @param Year $otherYear
     * @param bool $expected
     */
    public function it_supports_equal_function(
        Year $year,
        Year $otherYear,
        bool $expected
    ) {
        $this->assertEquals($expected, $year->equals($otherYear));
    }

    public function yearsProvider()
    {
        return [
            [
                new Year(2050),
                new Year(2050),
                true,
            ],
            [
                new Year(2050),
                new Year(2051),
                false,
            ],
        ];
    }
}
