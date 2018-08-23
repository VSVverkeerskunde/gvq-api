<?php declare(strict_types=1);

namespace VSV\GVQ_API\Models\ValueObjects;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
use VSV\GVQ_API\Question\ValueObjects\Year;

class TieBreaker
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var Year
     */
    private $year;

    /**
     * @var NotEmptyString
     */
    private $question;

    /**
     * @var PositiveNumber
     */
    private $answer;
}
