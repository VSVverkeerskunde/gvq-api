<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Models;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
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
    private $text;

    /**
     * @var NotEmptyString
     */
    private $imageFileName;

    /**
     * @var Answers
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
     * @param NotEmptyString $text
     * @param NotEmptyString $imageFileName
     * @param Answers $answers
     * @param NotEmptyString $feedback
     */
    public function __construct(
        UuidInterface $id,
        Language $language,
        Year $year,
        Category $category,
        NotEmptyString $text,
        NotEmptyString $imageFileName,
        Answers $answers,
        NotEmptyString $feedback
    ) {
        if (count($answers) < 2 || count($answers) > 3) {
            throw new \InvalidArgumentException('Amount of answers must be 2 or 3.');
        }

        $this->id = $id;
        $this->language = $language;
        $this->year = $year;
        $this->category = $category;
        $this->text = $text;
        $this->imageFileName = $imageFileName;
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
    public function getText(): NotEmptyString
    {
        return $this->text;
    }

    /**
     * @return NotEmptyString
     */
    public function getImageFileName(): NotEmptyString
    {
        return $this->imageFileName;
    }

    /**
     * @return Answers
     */
    public function getAnswers(): Answers
    {
        return $this->answers;
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
