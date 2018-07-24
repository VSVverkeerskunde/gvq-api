<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\ValueObjects;

use VSV\GVQ_API\User\ValueObjects\Email;

class QuizParticipant
{
    /**
     * @var Email
     */
    private $email;

    /**
     * @param Email $email
     */
    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * @param QuizParticipant $quizParticipant
     * @return bool
     */
    public function equals(QuizParticipant $quizParticipant): bool
    {
        return $this->email->equals($quizParticipant->getEmail());
    }
}
