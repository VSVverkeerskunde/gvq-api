<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\ValueObjects;

use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
{
    /**
     * @var Role $role
     */
    private $role;

    protected function setUp(): void
    {
        $this->role = new Role('admin');
    }

    /**
     * @test
     * @dataProvider
     */
    public function it_throws_for_unsupported_values(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid value: superuser for role.');

        new Role('superuser');
    }

    /**
     * @test
     * @dataProvider rolesProvider
     * @param Role $role
     * @param Role $otherRole
     * @param bool $expected
     */
    public function it_supports_equals_function(Role $role, Role $otherRole, $expected): void
    {
        $this->assertEquals(
            $expected,
            $role->equals($otherRole)
        );
    }

    /**
     * @return array[]
     */
    public function rolesProvider(): array
    {
        return [
            [
                new Role('admin'),
                new Role('admin'),
                true,
            ],
            [
                new Role('admin'),
                new Role('vsv'),
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
            'admin',
            $this->role->toNative()
        );
    }
}
