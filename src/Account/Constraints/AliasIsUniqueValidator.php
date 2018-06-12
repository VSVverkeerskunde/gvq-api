<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\Company\ValueObjects\Alias;

class AliasIsUniqueValidator extends ConstraintValidator
{
    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * @param CompanyRepository $companyRepository
     */
    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if ($constraint instanceof AliasIsUnique) {
            $company = $this->companyRepository->getByAlias(new Alias($value));
            if ($company != null) {
                $this->context->buildViolation($constraint->getMessage())
                    ->addViolation();
            }
        }
    }
}
