<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Serializers;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use VSV\GVQ_API\Question\Models\Answer;
use VSV\GVQ_API\Question\Models\Answers;
use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Question\ValueObjects\Year;

class QuestionDenormalizer implements DenormalizerInterface
{
    /**
     * @var CategoryDenormalizer
     */
    private $categoryDenormalizer;

    /**
     * @var AnswerDenormalizer
     */
    private $answerDenormalizer;

    /**
     * @param CategoryDenormalizer $categoryDenormalizer
     * @param AnswerDenormalizer $answerDenormalizer
     */
    public function __construct(
        CategoryDenormalizer $categoryDenormalizer,
        AnswerDenormalizer $answerDenormalizer
    ) {
        $this->categoryDenormalizer = $categoryDenormalizer;
        $this->answerDenormalizer = $answerDenormalizer;
    }


    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function denormalize($data, $class, $format = null, array $context = []): Question
    {
        $category = $this->categoryDenormalizer->denormalize(
            $data['category'],
            Category::class,
            $format,
            $context
        );

        $answers = array_map(
            function (array $answer) use ($format, $context) {
                return $this->answerDenormalizer->denormalize(
                    $answer,
                    Answer::class,
                    $format,
                    $context
                );
            },
            $data['answers']
        );

        return new Question(
            Uuid::fromString($data['id']),
            new Language($data['language']),
            new Year($data['year']),
            $category,
            new NotEmptyString($data['text']),
            new NotEmptyString($data['imageFileName']),
            new Answers(...$answers),
            new NotEmptyString($data['feedback']),
            new \DateTimeImmutable($data['createdOn'])
        );
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return ($type === Question::class) && ($format === 'json');
    }
}
