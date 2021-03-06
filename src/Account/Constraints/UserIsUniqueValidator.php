<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use VSV\GVQ_API\User\Repositories\UserRepository;
use VSV\GVQ_API\User\ValueObjects\Email;

class UserIsUniqueValidator extends ConstraintValidator
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @inheritdoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if ($constraint instanceof UserIsUnique) {
            $user = $this->userRepository->getByEmail(new Email($value));
            $raiseError = false;

            if ($user !== null) {
                // Don't take own e-mail address into account.
                if ($constraint->getUserId() === null) {
                    $raiseError = true;
                }
                // Take own e-mail address into account.
                if ($user->getId()->toString() !== $constraint->getUserId()) {
                    $raiseError = true;
                }
            }

            if ($raiseError) {
                $this->context->buildViolation($constraint->getMessage())
                    ->setParameter('{{ email }}', $value)
                    ->addViolation();
            }
        }
    }
}
