<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use VSV\GVQ_API\Question\Models\Answer;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Serializers\AnswerDenormalizer;
use VSV\GVQ_API\Question\Serializers\QuestionDenormalizer;
use VSV\GVQ_API\Quiz\Events\AbstractAnsweredEvent;
use VSV\GVQ_API\Quiz\Events\AnsweredIncorrect;

abstract class AbstractAnsweredEventDenormalizer implements DenormalizerInterface
{
    /**
     * @var QuestionDenormalizer
     */
    private $questionDenormalier;

    /**
     * @var AnswerDenormalizer
     */
    private $answerDenormalizer;

    /**
     * @param QuestionDenormalizer $questionDenormalizer
     * @param AnswerDenormalizer $answerDenormalizer
     */
    public function __construct(
        QuestionDenormalizer $questionDenormalizer,
        AnswerDenormalizer $answerDenormalizer
    ) {
        $this->questionDenormalier = $questionDenormalizer;
        $this->answerDenormalizer = $answerDenormalizer;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function denormalize($data, $class, $format = null, array $context = []): AbstractAnsweredEvent
    {
        $class = $this->getAnsweredEventClassName();

        return new $class(
            Uuid::fromString($data['id']),
            $this->questionDenormalier->denormalize(
                $data['question'],
                Question::class,
                'json'
            ),
            $this->answerDenormalizer->denormalize(
                $data['answer'],
                Answer::class,
                'json'
            ),
            new \DateTimeImmutable($data['answeredOn'])
        );
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        $class = $this->getAnsweredEventClassName();

        return ($type === $class) && ($format === 'json');
    }

    /**
     * @return string
     */
    abstract protected function getAnsweredEventClassName(): string;
}
