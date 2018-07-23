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
     * @return string
     */
    public function toNative(): string
    {
        return $this->email->toNative();
    }

    /**
     * @param QuizParticipant $quizParticipant
     * @return bool
     */
    public function equals(QuizParticipant $quizParticipant): bool
    {
        return $this->toNative() === $quizParticipant->toNative();
    }
}
