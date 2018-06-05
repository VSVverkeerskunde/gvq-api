<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Models;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;

class UsersTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_iterate_over_users(): void
    {
        $user1 = ModelsFactory::createUser();
        $user2 = ModelsFactory::createAlternateUser();

        $expectedUsers = [
            $user1,
            $user2,
        ];

        $users = new Users(...$expectedUsers);

        $actualUsers = [];
        foreach ($users as $user) {
            $actualUsers[] = $user;
        }

        $this->assertEquals($expectedUsers, $actualUsers);
    }
}
