<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Question\Models\Answer;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Quiz\Events\AnsweredCorrect;

class AnsweredCorrectDenormalizer extends AbstractAnsweredEventDenormalizer
{
    /**
     * @inheritdoc
     * @return AnsweredCorrect
     * @throws \Exception
     */
    public function denormalize($data, $class, $format = null, array $context = []): AnsweredCorrect
    {
        return new AnsweredCorrect(
            Uuid::fromString($data['id']),
            $this->questionDenormalizer->denormalize(
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
        return ($type === AnsweredCorrect::class) && ($format === 'json');
    }
}
