<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
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
    public function it_validates_with_unique_email(): void
    {
        $this->companyRepository
            ->method("getByName")
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
    public function it_fails_with_existing_company_name(array $options, string $expectedMessage): void
    {
        $constraint = new CompanyIsUnique($options);

        $this->companyRepository
            ->method("getByName")
            ->willReturn(ModelsFactory::createCompany());

        $this->validator->validate('CompanyName', $constraint);
        $this->buildViolation($expectedMessage)->assertRaised();
    }

    /**
     * @return array[]
     */
    public function constraintOptionsProvider(): array
    {
        return [
            [
                [
                    'message' => 'Deze bedrijfsnaam bestaat al',
                ],
                'Deze bedrijfsnaam bestaat al',
            ],
            [
                [],
                'This company name already exists.',
            ],
        ];
    }
}
