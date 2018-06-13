<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\User\Repositories\UserRepository;
use VSV\GVQ_API\User\ValueObjects\Email;

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
    public function it_succeeds_with_unique_email(): void
    {
        $this->userRepository
            ->expects($this->once())
            ->method("getByEmail")
            ->with(new Email('d@d.be'))
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
            ->expects($this->once())
            ->method("getByEmail")
            ->with(new Email('d@d.be'))
            ->willReturn(ModelsFactory::createUser());

        $this->validator->validate('d@d.be', $constraint);
        $this->buildViolation($expectedMessage)
            ->setParameter('{{ email }}', 'd@d.be')
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
                    'message' => 'Het e-mailadres "{{ email }}" bestaat al',
                ],
                'Het e-mailadres "{{ email }}" bestaat al',
            ],
            [
                [],
                'The email "{{ email }}" is already in use',
            ],
        ];
    }
}
