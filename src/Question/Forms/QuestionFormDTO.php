<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Forms;

use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Question\Models\Answer;
use VSV\GVQ_API\Question\Models\Answers;
use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\ValueObjects\Year;

class QuestionFormDTO
{
    /**
     * @var Language
     */
    public $language;

    /**
     * @var int
     */
    public $year;

    /**
     * @var Category
     */
    public $category;

    /**
     * @var UploadedFile
     */
    public $photo;

    /**
     * @var string
     */
    public $answer1;

    /**
     * @var string
     */
    public $answer2;

    /**
     * @var string
     */
    public $answer3;

    /**
     * @var int
     */
    public $correctAnswer;

    /**
     * @var string
     */
    public $text;

    /**
     * @var string
     */
    public $feedback;

    /**
     * QuestionForm constructor.
     */
    public function __construct()
    {
        $this->year = 2018;
    }

    /**
     * @param UuidFactoryInterface $uuidFactory
     * @return Question
     */
    public function toQuestion(UuidFactoryInterface $uuidFactory): Question
    {
        // TODO: Take into account the correct number of answers, can be 2 or 3.
        $answers = new Answers(
            new Answer(
                $uuidFactory->uuid4(),
                new NotEmptyString($this->answer1),
                $this->correctAnswer === 1 ? true : false
            ),
            new Answer(
                $uuidFactory->uuid4(),
                new NotEmptyString($this->answer2),
                $this->correctAnswer === 2 ? true : false
            ),
            new Answer(
                $uuidFactory->uuid4(),
                new NotEmptyString($this->answer3),
                $this->correctAnswer === 3 ? true : false
            )
        );

        return new Question(
            $uuidFactory->uuid4(),
            $this->language,
            new Year($this->year),
            $this->category,
            new NotEmptyString($this->text),
            new NotEmptyString('TODO.jpg'),
            $answers,
            new NotEmptyString($this->feedback)
        );
    }
}
