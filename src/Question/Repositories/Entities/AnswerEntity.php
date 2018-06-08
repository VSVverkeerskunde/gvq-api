<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories\Entities;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\Repositories\Entities\Entity;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
use VSV\GVQ_API\Question\Models\Answer;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;

/**
 * @ORM\Entity()
 * @ORM\Table(name="answer")
 */
class AnswerEntity extends Entity
{
    /**
     * @var int
     *
     * @ORM\Column(name="col_index", type="smallint", nullable=false)
     */
    private $index;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=1024, nullable=false)
     */
    private $text;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $correct;

    /**
     * @var QuestionEntity
     *
     * @ORM\ManyToOne(targetEntity="QuestionEntity", inversedBy="answerEntities")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id", nullable=false)
     */
    private $questionEntity;

    /**
     * @param string $id
     * @param int $index
     * @param string $text
     * @param bool $correct
     */
    private function __construct(
        string $id,
        int $index,
        string $text,
        bool $correct
    ) {
        parent::__construct($id);

        $this->index = $index;
        $this->text = $text;
        $this->correct = $correct;
    }

    /**
     * @param Answer $answer
     * @return AnswerEntity
     */
    public static function fromAnswer(Answer $answer): AnswerEntity
    {
        return new AnswerEntity(
            $answer->getId()->toString(),
            $answer->getIndex()->toNative(),
            $answer->getText()->toNative(),
            $answer->isCorrect()
        );
    }

    /**
     * @return Answer
     */
    public function toAnswer(): Answer
    {
        return new Answer(
            Uuid::fromString($this->getId()),
            new PositiveNumber($this->getIndex()),
            new NotEmptyString($this->getText()),
            $this->isCorrect()
        );
    }

    /**
     * @return int
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return bool
     */
    public function isCorrect(): bool
    {
        return $this->correct;
    }

    /**
     * @param QuestionEntity $questionEntity
     */
    public function setQuestionEntity(QuestionEntity $questionEntity): void
    {
        $this->questionEntity = $questionEntity;
    }
}
