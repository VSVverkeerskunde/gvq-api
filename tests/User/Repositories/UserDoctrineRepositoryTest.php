<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Repositories;

use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepositoryTest;
use VSV\GVQ_API\User\Models\User;
use VSV\GVQ_API\User\Repositories\Entities\UserEntity;
use VSV\GVQ_API\User\ValueObjects\Email;

class UserDoctrineRepositoryTest extends AbstractDoctrineRepositoryTest
{
    /**
     * @var UserDoctrineRepository
     */
    private $userDoctrineRepository;

    /**
     * @var User
     */
    private $user;

    /**
     * @var User
     */
    private $userWithPassword;

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->userDoctrineRepository = new UserDoctrineRepository(
            $this->entityManager
        );

        $this->user = ModelsFactory::createUser();
        $this->userWithPassword = ModelsFactory::createUserWithPassword();
    }

    /**
     * @inheritdoc
     */
    protected function getRepositoryName(): string
    {
        return UserEntity::class;
    }

    /**
     * @test
     */
    public function it_can_save_a_user(): void
    {
        $this->userDoctrineRepository->save($this->user);

        $foundUser = $this->userDoctrineRepository->getByEmail(
            new Email('admin@gvq.be')
        );

        $this->assertEquals($this->user, $foundUser);
    }

    /**
     * @test
     */
    public function it_can_save_a_user_with_password(): void
    {
        $this->userDoctrineRepository->save($this->userWithPassword);

        $foundUser = $this->userDoctrineRepository->getByEmail(
            new Email('admin@gvq.be')
        );

        $this->assertEquals($this->userWithPassword, $foundUser);
    }
}
