<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\Factory\ModelsFactory;

class CompanyIsUniqueValidatorTest extends ConstraintValidatorTestCase
{
    /**
     * @var CompanyRepository|MockObject
     */
    private $companyRepository;

    protected function createValidator()
    {
        /** @var CompanyRepository|MockObject $companyRepository */
        $companyRepository = $this->createMock(CompanyRepository::class);
        $this->companyRepository = $companyRepository;

        return new CompanyIsUniqueValidator($this->companyRepository);
    }

    /**
     * @test
     */
    public function it_succeeds_with_unique_email(): void
    {
        $this->companyRepository
            ->expects($this->once())
            ->method("getByName")
            ->with(new NotEmptyString('CompanyName'))
            ->willReturn(null);

        $this->validator->validate('CompanyName', new CompanyIsUnique());

        $this->assertNoViolation();
    }

    /**
     * @test
     * @dataProvider constraintOptionsProvider
     * @param array $options
     * @param string $expectedMessage
     */
    public function it_fails_with_existing_name_of_other_company(array $options, string $expectedMessage): void
    {
        $this->companyRepository
            ->expects($this->once())
            ->method("getByName")
            ->with(new NotEmptyString('CompanyName'))
            ->willReturn(ModelsFactory::createCompany());

        $this->validator->validate(
            'CompanyName',
            new CompanyIsUnique($options)
        );

        $this->buildViolation($expectedMessage)
            ->setParameter('{{ company }}', 'CompanyName')
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
                    'message' => 'De bedrijfsnaam "{{ company }}" bestaat al',
                ],
                'De bedrijfsnaam "{{ company }}" bestaat al',
            ],
            [
                [
                    'message' => 'De bedrijfsnaam "{{ company }}" bestaat al',
                    'companyId' => '0ffc0f85-78ee-496b-bc61-17be1326c768',
                ],
                'De bedrijfsnaam "{{ company }}" bestaat al',
            ],
            [
                [],
                'The company "{{ company }}" already exists.',
            ],
        ];
    }

    /**
     * @test
     */
    public function it_succeeds_with_existing_name_of_same_company(): void
    {
        $this->companyRepository
            ->expects($this->once())
            ->method("getByName")
            ->with(new NotEmptyString('CompanyName'))
            ->willReturn(ModelsFactory::createCompany());

        $constraint = new CompanyIsUnique(
            [
                'message' => 'The company "{{ company }}" already exists.',
                'companyId' => '85fec50a-71ed-4d12-8a69-28a3cf5eb106',
            ]
        );

        $this->validator->validate('CompanyName', $constraint);

        $this->assertNoViolation();
    }
}
