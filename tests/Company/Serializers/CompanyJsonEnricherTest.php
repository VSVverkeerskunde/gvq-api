<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Serializers;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactoryInterface;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\User\Repositories\UserRepository;

class CompanyJsonEnricherTest extends TestCase
{
    /**
     * @var UuidFactoryInterface|MockObject
     */
    private $uuidFactory;

    /**
     * @var UserRepository|MockObject
     */
    private $userRepository;

    /**
     * @var CompanyJsonEnricher
     */
    private $companyJsonEnricher;

    protected function setUp(): void
    {
        /** @var UuidFactoryInterface|MockObject $uuidFactory */
        $uuidFactory = $this->createMock(UuidFactoryInterface::class);
        $this->uuidFactory = $uuidFactory;

        /** @var UserRepository|MockObject $userRepository */
        $userRepository = $this->createMock(UserRepository::class);
        $this->userRepository = $userRepository;

        $this->companyJsonEnricher = new CompanyJsonEnricher(
            $this->uuidFactory,
            $this->userRepository
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_enrich_a_new_question_json()
    {
        $newCompanyJson = ModelsFactory::createJson('new_company');

        $this->uuidFactory->expects($this->exactly(3))
            ->method('uuid4')
            ->willReturnOnConsecutiveCalls(
                Uuid::fromString('85fec50a-71ed-4d12-8a69-28a3cf5eb106'),
                Uuid::fromString('827a7945-ffd0-433e-b843-721c98ab72b8'),
                Uuid::fromString('f99c7747-7c27-4388-b0ec-dba380363d23')
            );

        $userId = Uuid::fromString('3ffc0f85-78ee-496b-bc61-17be1326c768');
        $this->uuidFactory->expects($this->once())
            ->method('fromString')
            ->willReturn(
                $userId
            );

        $this->userRepository->expects($this->once())
            ->method('getById')
            ->with($userId)
            ->willReturn(ModelsFactory::createUser());

        $actualCompanyJson = $this->companyJsonEnricher->enrich($newCompanyJson);

        $expectedCompanyJson = ModelsFactory::createJson('company');
        $this->assertEquals(
            json_decode($expectedCompanyJson, true),
            json_decode($actualCompanyJson, true)
        );
    }
}
