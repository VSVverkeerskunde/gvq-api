<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\ValueObjects;

use PHPUnit\Framework\TestCase;

class AliasTest extends TestCase
{
    /**
     * @var Alias
     */
    private $aliasString;

    protected function setUp(): void
    {
        $this->aliasString = new Alias('abc-123');
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
            'Invalid value: '.$value.' for TranslatedAlias. '.
            'Value should be between 3 and 40 characters long and consist only of lowercase letters, numbers and "-"'
        );

        new Alias($value);
    }

    /**
     * @return string[][]
     */
    public function invalidAliasStringProvider(): array
    {
        return [
            [
                'ab',
            ],
            [
                'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
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
            new Alias($value)
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
     * @param Alias $aliasString
     * @param Alias $otherAliasString
     * @param bool $expected
     */
    public function it_supports_equals_function(
        Alias $aliasString,
        Alias $otherAliasString,
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
                new Alias('abc-123'),
                new Alias('abc-123'),
                true,
            ],
            [
                new Alias('abc-123'),
                new Alias('abc-1234'),
                false,
            ],
        ];
    }
}
