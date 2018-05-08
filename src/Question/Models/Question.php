<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Models;

use League\Uri\Interfaces\Uri;
use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Question\ValueObjects\Language;
use VSV\GVQ_API\Question\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Question\ValueObjects\Year;

class Question
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var Language
     */
    private $language;

    /**
     * @var Year
     */
    private $year;

    /**
     * @var Category
     */
    private $category;

    /**
     * @var NotEmptyString
     */
    private $questionText;

    /**
     * @var Uri
     */
    private $pictureUri;

    /**
     * @var Answer[]
     */
    private $answers;

    /**
     * @var NotEmptyString
     */
    private $feedback;

    /**
     * @var \DateTimeImmutable
     */
    private $archivedOn;

    /**
     * @param UuidInterface $id
     * @param Language $language
     * @param Year $year
     * @param Category $category
     * @param NotEmptyString $questionText
     * @param Uri $pictureUri
     * @param NotEmptyString $feedback
     * @param Answer ...$answers
     */
    public function __construct(
        UuidInterface $id,
        Language $language,
        Year $year,
        Category $category,
        NotEmptyString $questionText,
        Uri $pictureUri,
        NotEmptyString $feedback,
        Answer ...$answers
    ) {
        $this->id = $id;
        $this->language = $language;
        $this->year = $year;
        $this->category = $category;
        $this->questionText = $questionText;
        $this->pictureUri = $pictureUri;
        $this->answers = $answers;
        $this->feedback = $feedback;
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return Language
     */
    public function getLanguage(): Language
    {
        return $this->language;
    }

    /**
     * @return Year
     */
    public function getYear(): Year
    {
        return $this->year;
    }

    /**
     * @return Category
     */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @return NotEmptyString
     */
    public function getQuestionText(): NotEmptyString
    {
        return $this->questionText;
    }

    /**
     * @return Uri
     */
    public function getPictureUri(): Uri
    {
        return $this->pictureUri;
    }

    /**
     * @return Answers
     */
    public function getAnswers(): Answers
    {
        return new Answers(...$this->answers);
    }

    /**
     * @return NotEmptyString
     */
    public function getFeedback(): NotEmptyString
    {
        return $this->feedback;
    }

    /**
     * @param \DateTimeImmutable $archiveOn
     */
    public function archiveOn(\DateTimeImmutable $archiveOn): void
    {
        if ($this->archivedOn !== null) {
            throw new \DomainException(
                'The question with id: "'.$this->getId()->toString().'" was already archived.'
            );
        }

        $this->archivedOn = $archiveOn;
    }

    /**
     * @return bool
     */
    public function isArchived(): bool
    {
        return $this->archivedOn !== null;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getArchivedOn(): ?\DateTimeImmutable
    {
        return $this->archivedOn;
    }
}
