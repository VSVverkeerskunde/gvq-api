<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\ValueObjects;

use PHPUnit\Framework\TestCase;

class AliasStringTest extends TestCase
{
    /**
     * @var AliasString
     */
    private $aliasString;

    protected function setUp(): void
    {
        $this->aliasString = new AliasString('abc-123');
    }

    /**
     * @test
     * @dataProvider invalidAliasStringProvider
     * @param string $value
     */
    public function it_throws_for_unsupported_values(string $value)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid value: '.$value.' for AliasString. '.
            'Value should be between 4 and 20 characters long and consist only of lowercase letters, numbers and "-"'
        );

        new AliasString($value);
    }

    /**
     * @return string[][]
     */
    public function invalidAliasStringProvider(): array
    {
        return [
            [
                'abc',
            ],
            [
                'aaaaaaaaaaaaaaaaaaaaa',
            ],
            [
                'abc 123',
            ],
            [
                'abdA123',
            ],
            [
                'abc$123',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider validAliasStringProvider
     * @param string $value
     */
    public function it_supports_valid_values(string $value): void
    {
        $this->assertNotNull(
            new AliasString($value)
        );
    }

    /**
     * @return string[][]
     */
    public function validAliasStringProvider(): array
    {
        return [
            [
                'ab-c',
            ],
            [
                'aaaaaaaaaaaaaaaaaaaa',
            ],
            [
                'djd09-dk',
            ],
        ];
    }

    /**
     * @test
     */
    public function it_supports_to_native(): void
    {
        $this->assertEquals(
            'abc-123',
            $this->aliasString->toNative()
        );
    }

    /**
     * @test
     * @dataProvider AliasStringsProvider
     * @param AliasString $aliasString
     * @param AliasString $otherAliasString
     * @param bool $expected
     */
    public function it_supports_equals_function(
        AliasString $aliasString,
        AliasString $otherAliasString,
        bool $expected
    ): void {
        $this->assertEquals(
            $expected,
            $aliasString->equals($otherAliasString)
        );
    }

    /**
     * @return array[]
     */
    public function AliasStringsProvider(): array
    {
        return [
            [
                new AliasString('abc-123'),
                new AliasString('abc-123'),
                true,
            ],
            [
                new AliasString('abc-123'),
                new AliasString('abc-1234'),
                false,
            ],
        ];
    }
}
