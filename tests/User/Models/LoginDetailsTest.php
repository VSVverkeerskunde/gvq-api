<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Models;

use PHPUnit\Framework\TestCase;

class LoginDetailsTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider missingValuesProvider
     * @param array $values
     */
    public function it_throws_on_missing_values(array $values): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Both email and password are required.');

        new LoginDetails($values);
    }

    /**
     * @return array
     */
    public function missingValuesProvider(): array
    {
        return [
            [
                [
                    'email' => 'info@2dotstwice.be',
                ],
            ],
            [
                [
                    'password' => 'info123',
                ],
            ],
            [
                [],
            ]
        ];
    }

    /**
     * @test
     *
     * @dataProvider emptyValuesProvider
     * @param array $values
     */
    public function it_throws_on_empty_values(array $values): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Email and password can\'t be empty.');

        new LoginDetails($values);
    }

    /**
     * @return array
     */
    public function emptyValuesProvider(): array
    {
        return [
            [
                [
                    'email' => '',
                    'password' => 'info123',
                ],
            ],
            [
                [
                    'email' => 'info@2dotstwice.be',
                    'password' => '',
                ],
            ],
            [
                [
                    'email' => '',
                    'password' => '',
                ],
            ]
        ];
    }
}
