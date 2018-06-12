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
     * @param string $value
     * @param UserIsUnique|Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if ($constraint instanceof UserIsUnique) {
            try {
                $value = empty($value) ? ' ' : $value;
                $email = new Email($value);
            } catch (\Exception $e) {
                $this->context->buildViolation($constraint->getMessage())
                    ->addViolation();

                return;
            }
            $user = $this->userRepository->getByEmail($email);
            if ($user != null) {
                $this->context->buildViolation($constraint->getMessage())
                    ->addViolation();
            }
        }
    }
}
