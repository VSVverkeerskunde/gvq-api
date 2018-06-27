<?php declare(strict_types=1);

namespace VSV\GVQ_API\Command;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\User\Models\User;
use VSV\GVQ_API\User\Repositories\UserRepository;
use VSV\GVQ_API\User\ValueObjects\Password;

class SeedUsersCommandTest extends TestCase
{
    /**
     * @var SeedUsersCommand
     */
    private $seedUsersCommand;

    /**
     * @var UserRepository|MockObject
     */
    private $userRepository;

    protected function setUp(): void
    {
        /** @var UserRepository|MockObject $userRepository */
        $userRepository = $this->createMock(UserRepository::class);
        $this->userRepository = $userRepository;

        $this->seedUsersCommand = new SeedUsersCommand(
            $this->userRepository
        );
    }

    /**
     * @test
     */
    public function it_has_a_name(): void
    {
        $this->assertEquals(
            'gvq:seed-users',
            $this->seedUsersCommand->getName()
        );
    }

    /**
     * @test
     */
    public function it_has_a_description(): void
    {
        $this->assertEquals(
            'Create the fixed users.',
            $this->seedUsersCommand->getDescription()
        );
    }

    /**
     * @test
     */
    public function it_seeds_users(): void
    {
        $this->userRepository
            ->expects($this->exactly(2))
            ->method('save');

        $commandTester = new CommandTester($this->seedUsersCommand);
        $commandTester->execute(
            [
                'users_file' => __DIR__.'/../../src/Command/fixed_users.yaml.dist',
            ]
        );
    }
}
