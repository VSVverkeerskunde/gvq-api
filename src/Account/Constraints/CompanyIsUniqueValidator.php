<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;

class CompanyIsUniqueValidator extends ConstraintValidator
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
     * @param string $value
     * @param CompanyIsUnique|Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if ($constraint instanceof CompanyIsUnique) {
            $company = $this->companyRepository->getByName(new NotEmptyString($value));
            if ($company != null) {
                $this->context->buildViolation($constraint->getMessage())
                    ->addViolation();
            }
        }
    }
}
