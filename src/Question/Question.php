<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question;

use League\Uri\Interfaces\Uri;
use Ramsey\Uuid\UuidInterface;

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
     * @var NotEmptyString
     */
    private $questionText;

    /**
     * @var Uri
     */
    private $pictureUri;

    /**
     * @var Answers
     */
    private $answers;

    /**
     * @var NotEmptyString
     */
    private $feedback;

    /**
     * @param UuidInterface $id
     * @param Language $language
     * @param Year $year
     * @param NotEmptyString $questionText
     * @param Uri $pictureUri
     * @param Answers $answers
     * @param NotEmptyString $feedback
     */
    public function __construct(
        UuidInterface $id,
        Language $language,
        Year $year,
        NotEmptyString $questionText,
        Uri $pictureUri,
        Answers $answers,
        NotEmptyString $feedback
    ) {
        $this->id = $id;
        $this->language = $language;
        $this->year = $year;
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
        return $this->answers;
    }

    /**
     * @return NotEmptyString
     */
    public function getFeedback(): NotEmptyString
    {
        return $this->feedback;
    }
}
