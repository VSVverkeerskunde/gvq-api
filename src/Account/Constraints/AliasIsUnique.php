<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Constraints;

use Symfony\Component\Validator\Constraint;

class AliasIsUnique extends Constraint
{
    public $message = 'The alias "{{ alias }}" already exists.';

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @inheritdoc
     */
    public function validatedBy(): string
    {
        return get_class($this).'Validator';
    }
}
