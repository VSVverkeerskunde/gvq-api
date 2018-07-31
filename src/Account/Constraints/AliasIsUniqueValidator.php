<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
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
     * @inheritdoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if ($constraint instanceof AliasIsUnique) {
            $company = $this->companyRepository->getByAlias(new Alias($value));
            $raiseError = false;

            if ($company !== null) {
                // If new company, all records are taken into account
                if ($constraint->getCompanyId() === null) {
                    $raiseError = true;
                }

                // If existing company, exclude own alias
                if ($company->getId()->toString() !== $constraint->getCompanyId()) {
                    $raiseError = true;
                }
            }

            if ($raiseError) {
                $this->context->buildViolation($constraint->getMessage())
                    ->setParameter('{{ alias }}', $value)
                    ->addViolation();
            }
        }
    }
}
