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
    public $image;

    /**
     * @var string
     */
    public $text;

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
     * @param NotEmptyString $fileName
     * @return Question
     */
    public function toNewQuestion(
        UuidFactoryInterface $uuidFactory,
        NotEmptyString $fileName
    ): Question {
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
            $fileName,
            $answers,
            new NotEmptyString($this->feedback)
        );
    }

    /**
     * @param Question $existingQuestion
     * @return Question
     */
    public function toExistingQuestion(Question $existingQuestion): Question
    {
        // TODO: Take into account the correct number of answers, can be 2 or 3.
        $existingAnswers = $existingQuestion->getAnswers()->toArray();

        $answers = new Answers(
            new Answer(
                $existingAnswers[0]->getId(),
                new NotEmptyString($this->answer1),
                $this->correctAnswer === 1 ? true : false
            ),
            new Answer(
                $existingAnswers[1]->getId(),
                new NotEmptyString($this->answer2),
                $this->correctAnswer === 2 ? true : false
            ),
            new Answer(
                $existingAnswers[2]->getId(),
                new NotEmptyString($this->answer3),
                $this->correctAnswer === 3 ? true : false
            )
        );

        return new Question(
            $existingQuestion->getId(),
            $this->language,
            new Year($this->year),
            $this->category,
            new NotEmptyString($this->text),
            $existingQuestion->getImageFileName(),
            $answers,
            new NotEmptyString($this->feedback)
        );
    }

    /**
     * @param Question $question
     */
    public function fromQuestion(Question $question): void
    {
        $this->language = $question->getLanguage();
        $this->year = $question->getYear()->toNative();
        $this->category = $question->getCategory();

        $this->text = $question->getText()->toNative();

        $answers = $question->getAnswers()->toArray();
        $this->answer1 = $answers[0]->getText()->toNative();
        $this->answer2 = $answers[1]->getText()->toNative();
        $this->answer3 = $answers[2]->getText()->toNative();
        $this->correctAnswer = 1;

        $this->feedback = $question->getFeedback()->toNative();
    }
}
