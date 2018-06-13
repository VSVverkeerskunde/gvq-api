<?php declare(strict_types=1);

namespace VSV\GVQ_API\Command;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\User\Models\User;
use VSV\GVQ_API\User\Models\Users;
use VSV\GVQ_API\User\Repositories\UserRepository;
use VSV\GVQ_API\User\ValueObjects\Email;
use VSV\GVQ_API\User\ValueObjects\Password;
use VSV\GVQ_API\User\ValueObjects\Role;

class CreateFixedUsersCommand extends Command
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
    }

    protected function configure(): void
    {
        $this->setName('gvq:create-users')
            ->setDescription('Create the fixed admin, aswr and admin users.')
            ->addArgument('users_file', InputArgument::OPTIONAL, 'Yaml file with users');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('Creating users...');

        foreach ($this->getUsers($input) as $user) {
            $output->writeln('Creating user: '.$user->getEmail()->toNative());
            $this->userRepository->save($user);
        }

        $output->writeln('Finished creating users.');
    }

    /**
     * @param InputInterface $input
     * @return Users
     */
    private function getUsers(InputInterface $input): Users
    {
        $usersFile = $input->getArgument('users_file');
        if (!$usersFile) {
            $usersFile = __DIR__.'/../../extra/fixedUsers.yaml';
        }

        $usersAsYml = Yaml::parseFile($usersFile);
        $users = $this->createUsersFromYml($usersAsYml);

        return $users;
    }

    /**
     * @param array $usersAsYml
     * @return Users
     */
    private function createUsersFromYml(array $usersAsYml): Users
    {
        return new Users(
            ...array_map(
                function (array $userAsYml) {
                    $user = new User(
                        Uuid::fromString($userAsYml['id']),
                        new Email($userAsYml['email']),
                        new NotEmptyString($userAsYml['lastName']),
                        new NotEmptyString($userAsYml['firstName']),
                        new Role($userAsYml['role']),
                        new Language($userAsYml['language']),
                        true
                    );
                    $user = $user->withPassword(
                        Password::fromPlainText($userAsYml['password'])
                    );

                    return $user;
                },
                $usersAsYml
            )
        );
    }
}
