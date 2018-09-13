<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Models;

use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;
use VSV\GVQ_API\User\ValueObjects\Email;

class TopScore
{
    /**
     * @var Email
     */
    private $email;

    /**
     * @var NaturalNumber
     */
    private $score;

    public function __construct(Email $email, NaturalNumber $score)
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
     * @return NaturalNumber
     */
    public function getScore(): NaturalNumber
    {
        return $this->score;
    }
}
