<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics;

use VSV\GVQ_API\User\ValueObjects\Email;

class TopScore
{
    /**
     * @var Email
     */
    private $email;
    /**
     * @var int
     */
    private $score;

    public function __construct(Email $email, int $score)
    {
        $this->email = $email;
        $this->score = $score;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * @return int
     */
    public function getScore(): int
    {
        return $this->score;
    }
}
