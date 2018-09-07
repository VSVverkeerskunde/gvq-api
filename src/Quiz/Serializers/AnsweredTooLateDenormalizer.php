<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Serializers\QuestionDenormalizer;
use VSV\GVQ_API\Quiz\Events\AnsweredTooLate;

class AnsweredTooLateDenormalizer implements DenormalizerInterface
{
    /**
     * @var QuestionDenormalizer
     */
    private $questionDenormalizer;

    /**
     * @param QuestionDenormalizer $questionNormalizer
     */
    public function __construct(QuestionDenormalizer $questionNormalizer)
    {
        $this->questionDenormalizer = $questionNormalizer;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function denormalize($data, $class, $format = null, array $context = []): AnsweredTooLate
    {
        return new AnsweredTooLate(
            Uuid::fromString($data['id']),
            $this->questionDenormalizer->denormalize(
                $data['question'],
                Question::class,
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
        return ($type === AnsweredTooLate::class) && ($format === 'json');
    }
}
