<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Serializers\QuestionDenormalizer;
use VSV\GVQ_API\Quiz\Events\QuestionAsked;

class QuestionAskedDenormalizer implements DenormalizerInterface
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
     * @throws \Exception
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        return new QuestionAsked(
            Uuid::fromString($data['id']),
            $this->questionDenormalizer->denormalize(
                $data['question'],
                Question::class,
                $format,
                $context
            ),
            new \DateTimeImmutable($data['askedOn'])
        );
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return ($type === QuestionAsked::class) && ($format === 'json');
    }
}
