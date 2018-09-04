<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories\Entities;

use Doctrine\ORM\Mapping as ORM;
use VSV\GVQ_API\Statistics\EmployeeParticipation;

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
     * @ORM\Id()
     * @var string
     * @ORM\Column(type="string", length=255, unique=false, nullable=false)
     */
    private $email;

    public static function fromEmployeeParticipation(EmployeeParticipation $employeeParticipation): self
    {
        $entity = new self();
        $entity->companyId = $employeeParticipation->getCompanyId()->toString();
        $entity->email = $employeeParticipation->getEmail()->toNative();

        return $entity;
    }
}