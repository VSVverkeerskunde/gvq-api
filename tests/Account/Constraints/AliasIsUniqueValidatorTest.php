<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\Factory\ModelsFactory;

class AliasIsUniqueValidatorTest extends ConstraintValidatorTestCase
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

        return new AliasIsUniqueValidator($this->companyRepository);
    }

    /**
     * @test
     */
    public function it_validates_with_unique_alias(): void
    {
        $this->companyRepository
            ->method("getByAlias")
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
    public function it_fails_with_existing_alias(array $options, string $expectedMessage): void
    {
        $constraint = new AliasIsUnique($options);

        $this->companyRepository
            ->method("getByAlias")
            ->willReturn(ModelsFactory::createCompany());

        $this->validator->validate('company-alias', $constraint);
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
                    'message' => 'Deze alias bestaat al',
                ],
                'Deze alias bestaat al',
            ],
            [
                [],
                'This alias already exists.',
            ],
        ];
    }
}
