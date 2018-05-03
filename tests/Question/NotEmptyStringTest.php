<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question;

use PHPUnit\Framework\TestCase;

class NotEmptyStringTest extends TestCase
{
    /**
     * @var NotEmptyString
     */
    private $notEmptyString;

    protected function setUp()
    {
        $this->notEmptyString = new NotEmptyString('text');
    }

    /**
     * @test
     * @dataProvider stringProvider
     * @param string $string
     */
    public function it_throws_on_empty_argument(string $string)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Text argument cannot be empty.');

        new NotEmptyString($string);
    }

    /**
     * @return string[][]
     */
    public function stringProvider(): array
    {
        return [
            'spaces' => [
                ' ',
            ],
            'empty' => [
                '',
            ],
        ];
    }

    /**
     * @test
     */
    public function it_supports_to_native()
    {
        $this->assertEquals(
            'text',
            $this->notEmptyString->toNative()
        );
    }

    /**
     * @test
     * @dataProvider notEmptyStringsProvider
     * @param NotEmptyString $notEmptyString
     * @param NotEmptyString $otherNotEmptyString
     * @param bool $expected
     */
    public function it_supports_equals_function(
        NotEmptyString $notEmptyString,
        NotEmptyString $otherNotEmptyString,
        bool $expected
    ) {
        $this->assertEquals(
            $expected,
            $notEmptyString->equals($otherNotEmptyString)
        );
    }

    /**
     * @return array[]
     */
    public function notEmptyStringsProvider()
    {
        return [
            [
                new NotEmptyString('text'),
                new NotEmptyString('text'),
                true,
            ],
            [
                new NotEmptyString(' text '),
                new NotEmptyString('text'),
                true,
            ],
            [
                new NotEmptyString('text'),
                new NotEmptyString('text2'),
                false,
            ],
        ];
    }
}
