<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Events;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\User\ValueObjects\Email;

class EmailRegistered extends AbstractQuizEvent
{
    /**
     * @var Email
     */
    private $email;

    /**
     * QuestionAsked constructor.
     * @param UuidInterface $id
     * @param Email $email
     */
    public function __construct(
        UuidInterface $id,
        Email $email
    ) {
        parent::__construct($id);

        $this->email = $email;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }
}
