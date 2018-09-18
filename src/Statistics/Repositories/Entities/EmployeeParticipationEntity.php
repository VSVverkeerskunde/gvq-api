<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories\Entities;

use Doctrine\ORM\Mapping as ORM;
use VSV\GVQ_API\Statistics\Models\EmployeeParticipation;

/**
 * @ORM\Entity()
 * @ORM\Table(name="employee_participation")
 */
class EmployeeParticipationEntity
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(type="string", length=255, unique=false, nullable=false)
     */
    private $companyId;

    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(type="string", length=255, unique=false, nullable=false)
     */
    private $email;

    /**
     * @param string $companyId
     * @param string $email
     */
    private function __construct(string $companyId, string $email)
    {
        $this->companyId = $companyId;
        $this->email = $email;
    }

    /**
     * @param EmployeeParticipation $employeeParticipation
     * @return EmployeeParticipationEntity
     */
    public static function fromEmployeeParticipation(
        EmployeeParticipation $employeeParticipation
    ): EmployeeParticipationEntity {
        return new EmployeeParticipationEntity(
            $employeeParticipation->getCompanyId()->toString(),
            $employeeParticipation->getEmail()->toNative()
        );
    }
}
