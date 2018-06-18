<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\Company\ValueObjects\Alias;
use VSV\GVQ_API\Factory\ModelsFactory;

class AliasIsUniqueValidatorTest extends ConstraintValidatorTestCase
{
    /**
     * @var CompanyRepository|MockObject
     */
    private $companyRepository;

    /**
     * @return AliasIsUniqueValidator
     */
    protected function createValidator(): AliasIsUniqueValidator
    {
        /** @var CompanyRepository|MockObject $companyRepository */
        $companyRepository = $this->createMock(CompanyRepository::class);
        $this->companyRepository = $companyRepository;

        return new AliasIsUniqueValidator($this->companyRepository);
    }

    /**
     * @test
     */
    public function it_succeeds_with_unique_alias(): void
    {
        $this->companyRepository
            ->expects($this->once())
            ->method("getByAlias")
            ->with(new Alias('company-alias'))
            ->willReturn(null);

        $this->validator->validate('company-alias', new AliasIsUnique());

        $this->assertNoViolation();
    }

    /**
     * @test
     * @dataProvider constraintOptionsProvider
     * @param array $options
     * @param string $expectedMessage
     */
    public function it_fails_with_existing_alias_of_other_company(array $options, string $expectedMessage): void
    {
        $this->companyRepository
            ->expects($this->once())
            ->method("getByAlias")
            ->with(new Alias('company-alias'))
            ->willReturn(ModelsFactory::createCompany());

        $this->validator->validate(
            'company-alias',
            new AliasIsUnique($options)
        );

        $this->buildViolation($expectedMessage)
            ->setParameter('{{ alias }}', 'company-alias')
            ->assertRaised();
    }

    /**
     * @return array[]
     */
    public function constraintOptionsProvider(): array
    {
        return [
            [
                [
                    'message' => 'De alias "{{ alias }}" bestaat al',
                ],
                'De alias "{{ alias }}" bestaat al',
            ],
            [
                [
                    'message' => 'De alias "{{ alias }}" bestaat al',
                    'companyId' => '0ffc0f85-78ee-496b-bc61-17be1326c768',
                ],
                'De alias "{{ alias }}" bestaat al',
            ],
            [
                [],
                'The alias "{{ alias }}" already exists.',
            ],
        ];
    }

    /**
     * @test
     */
    public function it_succeeds_with_existing_alias_of_same_company(): void
    {
        $this->companyRepository
            ->expects($this->once())
            ->method("getByAlias")
            ->with(new Alias('company-alias'))
            ->willReturn(ModelsFactory::createCompany());

        $constraint = new AliasIsUnique(
            [
                'message' => 'The alias "{{ alias }}" already exists.',
                'companyId' => '85fec50a-71ed-4d12-8a69-28a3cf5eb106',
            ]
        );

        $this->validator->validate('company-alias', $constraint);

        $this->assertNoViolation();
    }
}
