<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Serializers;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactoryInterface;
use VSV\GVQ_API\Factory\ModelsFactory;

class RegistrationJsonEnricherTest extends TestCase
{
    /**
     * @var UuidFactoryInterface|MockObject
     */
    private $uuidFactory;

    /**
     * @var RegistrationJsonEnricher
     */
    private $registrationEnricher;

    public function setUp(): void
    {
        /** @var UuidFactoryInterface|MockObject $uuidFactory */
        $uuidFactory = $this->createMock(UuidFactoryInterface::class);
        $this->uuidFactory = $uuidFactory;

        $this->registrationEnricher = new RegistrationJsonEnricher(
            $this->uuidFactory
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_enrich_a_new_json_registration()
    {
        $newJsonRegistration = ModelsFactory::createJson('new_registration');

        $this->uuidFactory->expects($this->exactly(4))
            ->method('uuid4')
            ->willReturnOnConsecutiveCalls(
                Uuid::fromString('85fec50a-71ed-4d12-8a69-28a3cf5eb106'),
                Uuid::fromString('827a7945-ffd0-433e-b843-721c98ab72b8'),
                Uuid::fromString('f99c7747-7c27-4388-b0ec-dba380363d23'),
                Uuid::fromString('3ffc0f85-78ee-496b-bc61-17be1326c768')
            );

        $actualJsonRegistration = $this->registrationEnricher->enrich($newJsonRegistration);

        $expectedRegistration = ModelsFactory::createJson('registration');
        $this->assertEquals(
            json_decode($expectedRegistration, true),
            json_decode($actualJsonRegistration, true)
        );
    }
}
