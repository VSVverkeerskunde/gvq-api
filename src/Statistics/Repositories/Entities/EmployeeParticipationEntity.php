<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories\Entities;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Statistics\Models\EmployeeParticipation;
use VSV\GVQ_API\User\ValueObjects\Email;

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

    public function toEmployeeParticipation(): EmployeeParticipation
    {
        return new EmployeeParticipation(
            Uuid::fromString($this->companyId),
            new Email($this->email)
        );
    }
}
