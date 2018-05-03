<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question;

use PHPUnit\Framework\TestCase;

class YearTest extends TestCase
{
    /**
     * @var Year
     */
    private $year;

    protected function setUp()
    {
        $this->year = new Year(2050);
    }

    /**
     * @test
     * @dataProvider yearProvider
     * @param int $year
     */
    public function it_throws_for_unsupported_values(int $year)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid value '.$year.' for year, value has to be above 2018 and below 2100'
        );

        new Year($year);
    }

    /**
     * @return int[][]
     */
    public function yearProvider(): array
    {
        return [
            [
                2017,
            ],
            [
                2100,
            ],
        ];
    }

    /**
     * @test
     */
    public function it_supports_to_native()
    {
        $this->assertSame(
            2050,
            $this->year->toNative()
        );
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
        $this->assertEquals(
            $expected,
            $year->equals($otherYear)
        );
    }

    /**
     * @return array[]
     */
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
