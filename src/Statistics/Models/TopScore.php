<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Models;

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
        if ($score < 0) {
            throw new \InvalidArgumentException('score has to be at least zero');
        }

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
