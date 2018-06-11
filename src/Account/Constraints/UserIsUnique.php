<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Constraints;

use Symfony\Component\Validator\Constraint;

class UserIsUnique extends Constraint
{
    public $message = 'This email is already in use';

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}
