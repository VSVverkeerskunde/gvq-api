<?php declare(strict_types=1);

namespace VSV\GVQ_API\Registration\Models;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Registration\ValueObjects\UrlSuffix;

class RegistrationTest extends TestCase
{
    /**
     * @var Registration
     */
    private $registration;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $this->registration = ModelsFactory::createRegistration();
    }

    /**
     * @test
     */
    public function it_stores_an_id(): void
    {
        $this->assertEquals(
            Uuid::fromString('00f20af9-c2f5-4bfb-9424-5c0c29fbc2e3'),
            $this->registration->getId()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_url_suffix(): void
    {
        $this->assertEquals(
            new UrlSuffix('d2c63a605ae27c13e43e26fe2c97a36c4556846dd3ef'),
            $this->registration->getUrlSuffix()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_user(): void
    {
        $this->assertEquals(
            ModelsFactory::createUser(),
            $this->registration->getUser()
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_stores_a_created_on(): void
    {
        $this->assertEquals(
            new \DateTimeImmutable('2020-02-02', new \DateTimeZone('UTC')),
            $this->registration->getCreatedOn()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_password_reset_flag(): void
    {
        $this->assertFalse(
            $this->registration->isPasswordReset()
        );
    }
}
