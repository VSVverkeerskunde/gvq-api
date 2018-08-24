<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Models;

use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
use VSV\GVQ_API\Contest\ValueObjects\Address;
use VSV\GVQ_API\Contest\ValueObjects\ContestParticipant;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;

class ContestParticipation
{
    /**
     * @var Year
     */
    private $year;

    /**
     * @var QuizChannel
     */
    private $channel;

    /**
     * @var ContestParticipant
     */
    private $contestParticipant;

    /**
     * @var Address
     */
    private $address;

    /**
     * @var PositiveNumber
     */
    private $answer1;

    /**
     * @var PositiveNumber
     */
    private $answer2;
}
