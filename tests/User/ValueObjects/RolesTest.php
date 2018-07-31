<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\ValueObjects;

use PHPUnit\Framework\TestCase;

class RolesTest extends TestCase
{
    /**
     * @var Role[]
     */
    private $rolesArray;

    /**
     * @var Roles
     */
    private $roles;

    protected function setUp(): void
    {
        $this->rolesArray = [
            new Role('admin'),
            new Role('vsv'),
            new Role('contact')
        ];

        $this->roles = new Roles(...$this->rolesArray);
    }

    /**
     * @test
     */
    public function it_can_iterate_over_roles(): void
    {
        $actualRoles = [];
        foreach ($this->roles as $role) {
            $actualRoles[] = $role;
        }

        $this->assertEquals($this->rolesArray, $actualRoles);
    }

    /**
     * @test
     */
    public function it_can_be_counted(): void
    {
        $this->assertEquals(3, count($this->roles));
    }

    /**
     * @test
     */
    public function it_can_be_converted_to_an_array(): void
    {
        $this->assertEquals(
            $this->rolesArray,
            $this->roles->toArray()
        );
    }
}
