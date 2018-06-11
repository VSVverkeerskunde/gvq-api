<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\User\Repositories\UserRepository;

class UserIsUniqueValidatorTest extends ConstraintValidatorTestCase
{
    /**
     * @var UserRepository|MockObject
     */
    private $userRepository;

    protected function createValidator()
    {
        /** @var UserRepository|MockObject userRepository */
        $userRepository = $this->createMock(UserRepository::class);
        $this->userRepository = $userRepository;

        return new UserIsUniqueValidator($this->userRepository);
    }

    /**
     * @test
     */
    public function it_validates_with_unique_email(): void
    {
        $this->userRepository
            ->method("getByEmail")
            ->willReturn(null);

        $this->validator->validate('d@d.be', new UserIsUnique());
        $this->assertNoViolation();
    }

    /**
     * @test
     * @dataProvider constraintOptionsProvider
     * @param array $options
     * @param string $expectedMessage
     */
    public function it_fails_with_existing_email(array $options, string $expectedMessage): void
    {
        $constraint = new UserIsUnique($options);

        $this->userRepository
            ->method("getByEmail")
            ->willReturn(ModelsFactory::createUser());

        $this->validator->validate('d@d.be', $constraint);
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
                    'message' => 'Dit e-mailadres bestaat al',
                ],
                'Dit e-mailadres bestaat al',
            ],
            [
                [],
                'This email is already in use',
            ],
        ];
    }
}
