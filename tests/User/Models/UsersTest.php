<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Models;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;

class UsersTest extends TestCase
{
    /**
     * @var User[]
     */
    private $usersArray;

    /**
     * @var Users
     */
    private $users;

    protected function setUp(): void
    {
        $this->usersArray = [
            ModelsFactory::createUser(),
            ModelsFactory::createAlternateUser()
        ];

        $this->users = new Users(...$this->usersArray);
    }

    /**
     * @test
     */
    public function it_can_iterate_over_users(): void
    {
        $actualUsers = [];
        foreach ($this->users as $user) {
            $actualUsers[] = $user;
        }

        $this->assertEquals($this->usersArray, $actualUsers);
    }

    /**
     * @test
     */
    public function it_can_be_counted(): void
    {
        $this->assertEquals(2, count($this->users));
    }

    /**
     * @test
     */
    public function it_can_be_converted_to_an_array(): void
    {
        $this->assertEquals(
            $this->usersArray,
            $this->users->toArray()
        );
    }
}
