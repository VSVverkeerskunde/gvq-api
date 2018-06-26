<?php declare(strict_types=1);

namespace VSV\GVQ_API\Registration\Repositories;

use Doctrine\ORM\ORMInvalidArgumentException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepositoryTest;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Registration\Models\Registration;
use VSV\GVQ_API\Registration\Repositories\Entities\RegistrationEntity;
use VSV\GVQ_API\Registration\ValueObjects\UrlSuffix;
use VSV\GVQ_API\User\Repositories\UserDoctrineRepository;

class RegistrationDoctrineRepositoryTest extends AbstractDoctrineRepositoryTest
{
    /**
     * @var RegistrationDoctrineRepository
     */
    private $registrationDoctrineRepository;

    /**
     * @var UuidInterface
     */
    private $uuid;

    /**
     * @var Registration
     */
    private $registration;

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->registrationDoctrineRepository = new RegistrationDoctrineRepository(
            $this->entityManager
        );

        $userDoctrineRepository = new UserDoctrineRepository(
            $this->entityManager
        );

        $userDoctrineRepository->save(
            ModelsFactory::createUser()
        );

        $this->uuid = Uuid::fromString('00f20af9-c2f5-4bfb-9424-5c0c29fbc2e3');
        $this->registration = ModelsFactory::createRegistration();
    }

    /**
     * @inheritdoc
     */
    protected function getRepositoryName(): string
    {
        return RegistrationEntity::class;
    }

    /**
     * @test
     */
    public function it_can_save_a_registration(): void
    {
        $this->registrationDoctrineRepository->save($this->registration);

        $foundRegistration = $this->registrationDoctrineRepository->getById(
            $this->uuid
        );

        $this->assertEquals($this->registration, $foundRegistration);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_throws_on_saving_with_an_invalid_user(): void
    {
        $registration = ModelsFactory::createRegistrationWithAlternateUser();

        $this->expectException(ORMInvalidArgumentException::class);
        $this->expectExceptionMessage(
            'A new entity was found through the relationship'
        );

        $this->registrationDoctrineRepository->save($registration);
    }

    /**
     * @test
     */
    public function it_can_delete_a_registration(): void
    {
        $this->registrationDoctrineRepository->save($this->registration);

        $this->registrationDoctrineRepository->delete($this->uuid);

        $foundRegistration = $this->registrationDoctrineRepository->getById(
            $this->uuid
        );
        $this->assertNull($foundRegistration);
    }

    /**
     * @test
     */
    public function it_can_get_a_registration_by_url_suffix(): void
    {
        $this->registrationDoctrineRepository->save($this->registration);

        $foundRegistration = $this->registrationDoctrineRepository->getByUrlSuffix(
            new UrlSuffix('d2c63a605ae27c13e43e26fe2c97a36c4556846dd3ef')
        );

        $this->assertEquals(
            $this->registration,
            $foundRegistration
        );
    }
}
