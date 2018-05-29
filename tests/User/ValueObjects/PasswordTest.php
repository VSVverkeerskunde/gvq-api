<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\ValueObjects;

use PHPUnit\Framework\TestCase;

class PasswordTest extends TestCase
{
    /**
     * @var Password $password
     */
    private $password;

    protected function setUp(): void
    {
        $this->password = Password::fromPlainText('P4ssword');
    }

    /**
     * @test
     * @dataProvider invalidPasswordProvider
     * @param string $plainTextValue
     */
    public function it_throws_for_unsupported_values(string $plainTextValue): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid value for password. '.
            'Must be at least 8 characters long, contain at least one lowercase, '.
            'one uppercase and one non-alphabetical character and must not start or end with a space.'
        );

        Password::fromPlainText($plainTextValue);
    }

    /**
     * @return string[][]
     */
    public function invalidPasswordProvider(): array
    {
        return [
            [
                'invalidpass',
            ],
            [
                'Invalidpass',
            ],
            [
                'inv4lidpass',
            ],
            [
                'invalid',
            ],
            [
                ' Inv4lidpass',
            ],
            [
                'Inv4lidpass ',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider validPasswordProvider
     * @param string $clearTextValue
     */
    public function it_supports_allowed_values(string $clearTextValue): void
    {
        $this->assertNotNull(
            Password::fromPlainText($clearTextValue)
        );
    }

    /**
     * @return string[][]
     */
    public function validPasswordProvider(): array
    {
        return [
            [
                'va lidαP4ss',
            ],
            [
                'Validp4ss',
            ],
            [
                'dH6§dj à*8kdE',
            ],
            [
                'aA345678',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider inputPasswordProvider
     * @param string $plainTextPassword
     * @param bool $expected
     */
    public function it_verifies_a_plain_text_password(string $plainTextPassword, bool $expected): void
    {
        $this->assertEquals(
            $this->password->verifies($plainTextPassword),
            $expected
        );
    }

    /**
     * @return array[]
     */
    public function inputPasswordProvider(): array
    {
        return [
            [
                'P4ssword',
                true,
            ],
            [
                'p4ssword',
                false,
            ],
            [
                'dH6§dj à*8kdE',
                false,
            ],
        ];
    }
}
