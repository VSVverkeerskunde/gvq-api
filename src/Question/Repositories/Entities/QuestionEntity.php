<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use League\Uri\Uri;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Question\Models\Answer;
use VSV\GVQ_API\Question\Models\Answers;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\ValueObjects\Language;
use VSV\GVQ_API\Question\ValueObjects\NotEmptyString;
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
     * @ORM\Column(name="picture_uri", type="string", length=255, nullable=false)
     */
    private $pictureUri;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AnswerEntity", mappedBy="questionEntity", cascade={"all"})
     */
    private $answerEntities;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=1024, nullable=false)
     */
    private $feedback;

    /**
     * @param string $id
     * @param string $language
     * @param int $year
     * @param CategoryEntity $categoryEntity
     * @param string $text
     * @param string $pictureUri
     * @param Collection $answerEntities
     * @param string $feedback
     */
    private function __construct(
        string $id,
        string $language,
        int $year,
        CategoryEntity $categoryEntity,
        string $text,
        string $pictureUri,
        Collection $answerEntities,
        string $feedback
    ) {
        parent::__construct($id);

        $this->language = $language;
        $this->year = $year;
        $this->categoryEntity = $categoryEntity;
        $this->text = $text;
        $this->pictureUri = $pictureUri;
        $this->answerEntities = $answerEntities;
        $this->feedback = $feedback;
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
            $question->getPictureUri()->__toString(),
            new ArrayCollection($answerEntities),
            $question->getFeedback()->toNative()
        );

        foreach ($answerEntities as $answerEntity) {
            $answerEntity->setQuestionEntity($questionEntity);
        }

        return $questionEntity;
    }

    /**
     * @return Question
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

        // Call toString method to solve side effect in Uri lib because the data
        // property gets created on calling this getter method.
        // @see https://github.com/thephpleague/uri-schemes/issues/10
        $pictureUri = Uri::createFromString($this->getPictureUri());
        $pictureUri->__toString();

        return new Question(
            Uuid::fromString($this->getId()),
            new Language($this->getLanguage()),
            new Year($this->getYear()),
            $this->getCategoryEntity()->toCategory(),
            new NotEmptyString($this->getText()),
            $pictureUri,
            $answers,
            new NotEmptyString($this->getFeedback())
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
     * @param CategoryEntity $categoryEntity
     */
    public function setCategoryEntity(CategoryEntity $categoryEntity): void
    {
        $this->categoryEntity = $categoryEntity;
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
    public function getPictureUri(): string
    {
        return $this->pictureUri;
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
}
