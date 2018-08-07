<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Repositories\Entities;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\User\ValueObjects\Role;

class UserEntityTest extends TestCase
{
    /**
     * @var UserEntity
     */
    private $userEntity;

    protected function setUp(): void
    {
        $this->userEntity = $this->createUserEntityWithRole(Role::CONTACT);
    }

    /**
     * @test
     */
    public function it_can_return_a_salt()
    {
        $this->assertNull($this->userEntity->getSalt());
    }

    /**
     * @test
     */
    public function it_can_return_a_user_name()
    {
        $this->assertEquals(
            'john@gvq.be',
            $this->userEntity->getEmail()
        );
    }

    /**
     * @test
     */
    public function it_can_erase_credentials()
    {
        $this->assertEquals(
            'password',
            $this->userEntity->getPassword()
        );

        $this->userEntity->eraseCredentials();

        $this->assertNull($this->userEntity->getPassword());
    }

    /**
     * @test
     * @dataProvider roleProvider
     * @param string $role
     * @param string $symfonyRole
     */
    public function it_can_return_a_list_of_roles(
        string $role,
        string $symfonyRole
    ) {
        $this->assertEquals(
            [$symfonyRole],
            $this->createUserEntityWithRole($role)->getRoles()
        );
    }

    /**
     * @return string[][]
     */
    public function roleProvider(): array
    {
        return [
            [
                Role::CONTACT,
                'ROLE_CONTACT',
            ],
            [
                Role::VSV,
                'ROLE_VSV',
            ],
            [
                Role::ADMIN,
                'ROLE_ADMIN',
            ],
        ];
    }

    /**
     * @param string $role
     * @return UserEntity
     */
    private function createUserEntityWithRole(string $role)
    {
        return new UserEntity(
            '3ffc0f85-78ee-496b-bc61-17be1326c768',
            'john@gvq.be',
            'Doe',
            'John',
            $role,
            Language::NL,
            'password',
            true
        );
    }
}
