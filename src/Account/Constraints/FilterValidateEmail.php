<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Constraints;

use Symfony\Component\Validator\Constraint;

class FilterValidateEmail extends Constraint
{
    public $message = 'This email value is invalid.';

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}
