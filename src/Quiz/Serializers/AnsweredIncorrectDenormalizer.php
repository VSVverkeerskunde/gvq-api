<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Question\Models\Answer;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Quiz\Events\AnsweredIncorrect;

class AnsweredIncorrectDenormalizer extends AbstractAnsweredEventDenormalizer
{
    /**
     * @inheritdoc
     * @return AnsweredIncorrect
     * @throws \Exception
     */
    public function denormalize($data, $class, $format = null, array $context = []): AnsweredIncorrect
    {
        return new AnsweredIncorrect(
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
            new \DateTimeImmutable($data['answeredOn']),
            $data['answeredTooLate']
        );
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return ($type === AnsweredIncorrect::class) && ($format === 'json');
    }
}
