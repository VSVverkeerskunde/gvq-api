<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\ValueObjects;

use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    /**
     * @var Email $email
     */
    private $email;

    protected function setUp(): void
    {
        $this->email = new Email('admin@gvq.be');
    }

    /**
     * @test
     * @dataProvider invalidEmailProvider
     * @param string $value
     */
    public function it_throws_for_unsupported_values(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid value '.$value.' for email');

        new Email($value);
    }

    /**
     * @return string[][]
     */
    public function invalidEmailProvider(): array
    {
        return [
            [
                '',
            ],
            [
                ' ',
            ],
            [
                'invalid',
            ],
            [
                'invalid@',
            ],
            [
                'd@invalid',
            ],
            [
                '@invalid.be',
            ],
            [
                'd@*',
            ],
            [
                'd@in valid',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider emailsProvider
     * @param Email $email
     * @param Email $otherEmail
     * @param bool $expected
     */
    public function it_supports_equals_function(Email $email, Email $otherEmail, $expected): void
    {
        $this->assertEquals(
            $expected,
            $email->equals($otherEmail)
        );
    }

    /**
     * @return array[]
     */
    public function emailsProvider(): array
    {
        return [
            [
                new Email('test@test.be'),
                new Email('test@test.be'),
                true,
            ],
            [
                new Email('test@test.be'),
                new Email('different@test.be'),
                false,
            ],
        ];
    }

    /**
     * @test
     */
    public function it_supports_to_native(): void
    {
        $this->assertEquals(
            'admin@gvq.be',
            $this->email->toNative()
        );
    }
}
