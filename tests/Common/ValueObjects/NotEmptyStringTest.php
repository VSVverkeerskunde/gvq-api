<?php declare(strict_types=1);

namespace VSV\GVQ_API\Common\ValueObjects;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;

class NotEmptyStringTest extends TestCase
{
    /**
     * @var NotEmptyString
     */
    private $notEmptyString;

    protected function setUp(): void
    {
        $this->notEmptyString = new NotEmptyString('text');
    }

    /**
     * @test
     * @dataProvider stringProvider
     * @param string $string
     */
    public function it_throws_on_empty_argument(string $string): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The string value cannot be empty.');

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
    public function it_supports_to_native(): void
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
    ): void {
        $this->assertEquals(
            $expected,
            $notEmptyString->equals($otherNotEmptyString)
        );
    }

    /**
     * @return array[]
     */
    public function notEmptyStringsProvider(): array
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
