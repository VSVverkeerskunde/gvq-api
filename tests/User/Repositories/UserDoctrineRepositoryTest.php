<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Repositories;

use Doctrine\ORM\EntityNotFoundException;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepositoryTest;
use VSV\GVQ_API\User\Models\User;
use VSV\GVQ_API\User\Models\Users;
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

        $foundUser = $this->userDoctrineRepository->getById(
            Uuid::fromString('3ffc0f85-78ee-496b-bc61-17be1326c768')
        );

        $this->assertEquals($this->user, $foundUser);
    }

    /**
     * @test
     */
    public function it_can_save_a_user_with_password(): void
    {
        $this->userDoctrineRepository->save($this->userWithPassword);

        $foundUser = $this->userDoctrineRepository->getById(
            Uuid::fromString('3ffc0f85-78ee-496b-bc61-17be1326c768')
        );

        $this->assertEquals($this->userWithPassword, $foundUser);
    }

    /**
     * @test
     */
    public function it_can_get_a_user_by_email(): void
    {
        $this->userDoctrineRepository->save($this->userWithPassword);

        $foundUser = $this->userDoctrineRepository->getByEmail(
            new Email('john@gvq.be')
        );

        $this->assertEquals($this->userWithPassword, $foundUser);
    }

    /**
     * @test
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function it_can_update_a_user(): void
    {
        $this->userDoctrineRepository->save($this->user);

        $updatedUser = ModelsFactory::createUpdatedUser();

        $this->userDoctrineRepository->update($updatedUser);

        $foundUser = $this->userDoctrineRepository->getById(
            Uuid::fromString('3ffc0f85-78ee-496b-bc61-17be1326c768')
        );

        $this->assertEquals($updatedUser, $foundUser);
    }

    /**
     * @test
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function it_can_update_a_user_and_keeps_existing_password(): void
    {
        $this->userDoctrineRepository->save($this->userWithPassword);

        $updatedUser = ModelsFactory::createUpdatedUser();

        $this->userDoctrineRepository->update($updatedUser);

        $foundUser = $this->userDoctrineRepository->getById(
            Uuid::fromString('3ffc0f85-78ee-496b-bc61-17be1326c768')
        );

        if ($this->userWithPassword->getPassword()) {
            $updatedUser = $updatedUser->withPassword(
                $this->userWithPassword->getPassword()
            );
        }
        $this->assertEquals($updatedUser, $foundUser);
    }

    /**
     * @test
     * @throws EntityNotFoundException
     */
    public function it_throws_on_updating_a_non_existing_user(): void
    {
        $wrongUser = ModelsFactory::createUpdatedUser();

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Invalid user supplied');

        $this->userDoctrineRepository->update($wrongUser);
    }

    /**
     * @test
     */
    public function it_can_get_all_users(): void
    {
        $this->userDoctrineRepository->save($this->user);

        $user2 = ModelsFactory::createAlternateUser();
        $this->userDoctrineRepository->save($user2);

        $foundUsers = $this->userDoctrineRepository->getAll();

        $this->assertEquals(
            new Users($this->user, $user2),
            $foundUsers
        );
    }

    /**
     * @test
     */
    public function it_returns_null_when_no_users_present(): void
    {
        $foundUsers = $this->userDoctrineRepository->getAll();

        $this->assertNull($foundUsers);
    }

    /**
     * @test
     * @throws EntityNotFoundException
     */
    public function it_can_update_a_user_password(): void
    {
        $this->userDoctrineRepository->save($this->userWithPassword);

        $updatedUser = ModelsFactory::createUserWithAlternatePassword();

        $this->userDoctrineRepository->updatePassword($updatedUser);

        $foundUser = $this->userDoctrineRepository->getById(
            Uuid::fromString('3ffc0f85-78ee-496b-bc61-17be1326c768')
        );

        $this->assertTrue(
            $foundUser->getPassword()->verifies('newPassw0rD'));
    }

    /**
     * @test
     * @throws EntityNotFoundException
     */
    public function it_throws_on_updating_the_password_of_a_non_existing_user(): void
    {
        $wrongUser = ModelsFactory::createUpdatedUser();

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Invalid user supplied');

        $this->userDoctrineRepository->updatePassword($wrongUser);
    }
}
