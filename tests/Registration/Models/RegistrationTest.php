<?php declare(strict_types=1);

namespace VSV\GVQ_API\Registration\Models;

use PHPUnit\Framework\TestCase;
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
    public function it_stores_a_hashcode(): void
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
            new \DateTimeImmutable('2020-02-02', new \DateTimeZone('GMT+1')),
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
