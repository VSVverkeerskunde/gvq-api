<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Constraints;

use Symfony\Component\Validator\Constraint;

class CompanyIsUnique extends Constraint
{
    /**
     * @var string|null
     *
     * When a company id is specified as a constraint option then the
     * validation doesn't fail if the found company by name has the same id.
     */
    public $companyId = null;

    /**
     * @var string
     */
    public $message = 'The company "{{ company }}" already exists.';

    /**
     * @return null|string
     */
    public function getCompanyId(): ?string
    {
        return $this->companyId;
    }

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
