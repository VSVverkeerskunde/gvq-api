<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Constraints;

use Symfony\Component\Validator\Constraint;

class UserIsUnique extends Constraint
{
    /**
     * @var string
     */
    public $message = 'The email "{{ email }}" is already in use';

    /**
     * @var string
     *
     * When a user id is specified as a constraint option then the
     * validation doesn't fail if the found user by e-mail has the same id.
     */
    public $userId = null;

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return null|string
     */
    public function getUserId(): ?string
    {
        return $this->userId;
    }

    /**
     * @inheritdoc
     */
    public function validatedBy(): string
    {
        return get_class($this).'Validator';
    }
}
