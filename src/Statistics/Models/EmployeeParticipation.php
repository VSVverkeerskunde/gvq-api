<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Models;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\User\ValueObjects\Email;

class EmployeeParticipation
{
    /**
     * @var UuidInterface
     */
    private $companyId;

    /**
     * @var Email
     */
    private $email;

    /**
     * @param UuidInterface $companyId
     * @param Email $email
     */
    public function __construct(UuidInterface $companyId, Email $email)
    {
        $this->companyId = $companyId;
        $this->email = $email;
    }

    /**
     * @return UuidInterface
     */
    public function getCompanyId(): UuidInterface
    {
        return $this->companyId;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }
}
