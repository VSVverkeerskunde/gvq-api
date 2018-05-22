<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Models;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\User\ValueObjects\Email;
use VSV\GVQ_API\User\ValueObjects\Password;
use VSV\GVQ_API\User\ValueObjects\Role;

class UserTest extends TestCase
{
    /**
     * @var User
     */
    private $user;

    protected function setUp(): void
    {
        $this->user = ModelsFactory::createUser();
    }

    /**
     * @test
     */
    public function it_can_store_an_id(): void
    {
        $this->assertEquals(
            Uuid::fromString('3ffc0f85-78ee-496b-bc61-17be1326c768'),
            $this->user->getId()
        );
    }

    /**
     * @test
     */
    public function it_can_store_an_email(): void
    {
        $this->assertEquals(
            new Email('admin@gvq.be'),
            $this->user->getEmail()
        );
    }

    /**
     * @test
     */
    public function it_can_store_a_password(): void
    {
        $this->assertEquals(
            Password::fromHash('$2y$10$Hcfuxvnmk60VO0SKOsvQhuNBP/jJi6.eecdZnqVWCKVt8XNW7mEeO'),
            $this->user->getPassword()
        );
    }

    /**
     * @test
     */
    public function it_can_store_a_first_name(): void
    {
        $this->assertEquals(
            new NotEmptyString('John'),
            $this->user->getFirstName()
        );
    }

    /**
     * @test
     */
    public function it_can_store_a_last_name(): void
    {
        $this->assertEquals(
            new NotEmptyString('Doe'),
            $this->user->getLastName()
        );
    }

    /**
     * @test
     */
    public function it_can_store_a_role(): void
    {
        $this->assertEquals(
            new Role('admin'),
            $this->user->getRole()
        );
    }
}
