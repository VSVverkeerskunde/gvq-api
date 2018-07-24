<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Models\Questions;
use VSV\GVQ_API\Question\Serializers\QuestionDenormalizer;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Quiz\Models\Quiz;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
use VSV\GVQ_API\Quiz\ValueObjects\QuizParticipant;
use VSV\GVQ_API\Quiz\ValueObjects\QuizType;
use VSV\GVQ_API\User\ValueObjects\Email;

class QuizDenormalizer implements DenormalizerInterface
{
    /**
     * @var QuestionDenormalizer
     */
    private $questionDenormalizer;

    /**
     * @param QuestionDenormalizer $questionDenormalizer
     */
    public function __construct(QuestionDenormalizer $questionDenormalizer)
    {
        $this->questionDenormalizer = $questionDenormalizer;
    }

    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = array()): Quiz
    {
        $questions = array_map(
            function (array $question) use ($format, $context) {
                return $this->questionDenormalizer->denormalize(
                    $question,
                    Question::class,
                    $format,
                    $context
                );
            },
            $data['questions']
        );

        return new Quiz(
            Uuid::fromString($data['id']),
            new QuizParticipant(new Email($data['participant'])),
            new QuizType($data['type']),
            new QuizChannel($data['channel']),
            new Language($data['language']),
            new Year($data['year']),
            new Questions(...$questions)
        );
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return ($type === Quiz::class) && ($format === 'json');
    }
}
