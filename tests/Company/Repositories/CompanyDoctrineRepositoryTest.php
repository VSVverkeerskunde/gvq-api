<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Repositories;

use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepositoryTest;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Repositories\Entities\CompanyEntity;
use VSV\GVQ_API\Company\ValueObjects\Alias;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\User\Repositories\UserDoctrineRepository;

class CompanyDoctrineRepositoryTest extends AbstractDoctrineRepositoryTest
{
    /**
     * @var CompanyDoctrineRepository
     */
    private $companyDoctrineRepository;

    /**
     * @var Company
     */
    private $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyDoctrineRepository = new CompanyDoctrineRepository(
            $this->entityManager
        );

        $userRepository = new UserDoctrineRepository(
            $this->entityManager
        );
        $user = ModelsFactory::createUser();
        $userRepository->save($user);

        $this->company = ModelsFactory::createCompany();
    }

    /**
     * @inheritdoc
     */
    protected function getRepositoryName(): string
    {
        return CompanyEntity::class;
    }

    /**
     * @test
     */
    public function it_can_save_a_company(): void
    {
        $this->companyDoctrineRepository->save($this->company);

        $foundCompany = $this->companyDoctrineRepository->getById(
            Uuid::fromString('85fec50a-71ed-4d12-8a69-28a3cf5eb106')
        );

        $this->assertEquals($this->company, $foundCompany);
    }

    /**
     * @test
     */
    public function it_throws_on_saving_a_company_with_non_existing_user(): void
    {
        $company = ModelsFactory::createCompanyWithAlternateUser();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'User with id: '.
            $company->getUser()->getId()->toString().
            ' not found.'
        );

        $this->companyDoctrineRepository->save($company);
    }

    /**
     * @test
     * @param NotEmptyString $name
     * @param Company|null $expectedResult
     * @dataProvider nameProvider
     */
    public function it_can_get_a_company_by_name(NotEmptyString $name, ?Company $expectedResult): void
    {
        $this->companyDoctrineRepository->save($this->company);

        $foundCompany = $this->companyDoctrineRepository->getByName($name);

        $this->assertEquals(
            $expectedResult,
            $foundCompany
        );
    }

    /**
     * @return array[]
     */
    public function nameProvider(): array
    {
        return [
            [
                new NotEmptyString('Vlaamse Stichting Verkeerskunde'),
                ModelsFactory::createCompany(),
            ],
            [
                new NotEmptyString('alternate'),
                null,
            ],
        ];
    }

    /**
     * @test
     * @param Alias $alias
     * @param Company|null $expectedResult
     * @dataProvider aliasProvider
     */
    public function it_can_get_a_company_by_alias(Alias $alias, ?Company $expectedResult): void
    {
        $this->companyDoctrineRepository->save($this->company);

        $foundCompany = $this->companyDoctrineRepository->getByAlias($alias);

        $this->assertEquals(
            $expectedResult,
            $foundCompany
        );
    }

    /**
     * @return array[]
     */
    public function aliasProvider(): array
    {
        return [
            [
                new Alias('vsv'),
                ModelsFactory::createCompany(),
            ],
            [
                new Alias('alternate'),
                null,
            ],
        ];
    }
}
