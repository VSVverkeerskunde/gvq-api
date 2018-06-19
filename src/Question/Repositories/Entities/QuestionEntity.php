<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\Repositories\Entities\Entity;
use VSV\GVQ_API\Question\Models\Answer;
use VSV\GVQ_API\Question\Models\Answers;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Question\ValueObjects\Year;

/**
 * @ORM\Entity()
 * @ORM\Table(name="question")
 */
class QuestionEntity extends Entity
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=2, nullable=false)
     */
    private $language;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=false)
     */
    private $year;

    /**
     * @var CategoryEntity
     *
     * @ORM\ManyToOne(targetEntity="CategoryEntity", fetch="EAGER")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     */
    private $categoryEntity;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=1024, nullable=false)
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="image_file_name", type="string", length=255, nullable=false)
     */
    private $imageFileName;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AnswerEntity", mappedBy="questionEntity", fetch="EAGER", cascade={"all"})
     */
    private $answerEntities;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=1024, nullable=false)
     */
    private $feedback;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable", name="created_on", nullable=false)
     */
    private $createdOn;

    /**
     * @param string $id
     * @param string $language
     * @param int $year
     * @param CategoryEntity $categoryEntity
     * @param string $text
     * @param string $imageFileName
     * @param Collection $answerEntities
     * @param string $feedback
     * @param \DateTimeImmutable $createdOn
     */
    private function __construct(
        string $id,
        string $language,
        int $year,
        CategoryEntity $categoryEntity,
        string $text,
        string $imageFileName,
        Collection $answerEntities,
        string $feedback,
        \DateTimeImmutable $createdOn
    ) {
        parent::__construct($id);

        $this->language = $language;
        $this->year = $year;
        $this->categoryEntity = $categoryEntity;
        $this->text = $text;
        $this->imageFileName = $imageFileName;
        $this->answerEntities = $answerEntities;
        $this->feedback = $feedback;
        $this->createdOn = $createdOn;

        foreach ($answerEntities as $answerEntity) {
            $answerEntity->setQuestionEntity($this);
        }
    }

    /**
     * @param Question $question
     * @return QuestionEntity
     */
    public static function fromQuestion(Question $question): QuestionEntity
    {
        /** @var AnswerEntity[] $answerEntities */
        $answerEntities = array_map(
            function (Answer $answer) {
                return AnswerEntity::fromAnswer($answer);
            },
            $question->getAnswers()->toArray()
        );

        $questionEntity = new QuestionEntity(
            $question->getId()->toString(),
            $question->getLanguage()->toNative(),
            $question->getYear()->toNative(),
            CategoryEntity::fromCategory($question->getCategory()),
            $question->getText()->toNative(),
            $question->getImageFileName()->toNative(),
            new ArrayCollection($answerEntities),
            $question->getFeedback()->toNative(),
            $question->getCreatedOn()
        );

        return $questionEntity;
    }

    /**
     * @return Question
     * @throws \Exception
     */
    public function toQuestion(): Question
    {
        $answers = new Answers(
            ...array_map(
                function (AnswerEntity $answerEntity) {
                    return $answerEntity->toAnswer();
                },
                $this->getAnswerEntities()->toArray()
            )
        );

        return new Question(
            Uuid::fromString($this->getId()),
            new Language($this->getLanguage()),
            new Year($this->getYear()),
            $this->getCategoryEntity()->toCategory(),
            new NotEmptyString($this->getText()),
            new NotEmptyString($this->getImageFileName()),
            $answers,
            new NotEmptyString($this->getFeedback()),
            $this->getCreatedOn()
        );
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * @return CategoryEntity
     */
    public function getCategoryEntity(): CategoryEntity
    {
        return $this->categoryEntity;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getImageFileName(): string
    {
        return $this->imageFileName;
    }

    /**
     * @return Collection
     */
    public function getAnswerEntities(): Collection
    {
        return $this->answerEntities;
    }

    /**
     * @return string
     */
    public function getFeedback(): string
    {
        return $this->feedback;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedOn(): \DateTimeImmutable
    {
        return $this->createdOn;
    }
}
