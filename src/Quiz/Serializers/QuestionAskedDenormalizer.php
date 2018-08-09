<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Serializers\QuestionDenormalizer;
use VSV\GVQ_API\Quiz\Events\QuestionAsked;
use VSV\GVQ_API\Quiz\ValueObjects\QuestionResult;

class QuestionAskedDenormalizer implements DenormalizerInterface
{
    /**
     * @var QuestionResultDenormalizer
     */
    private $questionResultDenormalizer;

    /**
     * @param QuestionResultDenormalizer $questionResultDenormalizer
     */
    public function __construct(QuestionResultDenormalizer $questionResultDenormalizer)
    {
        $this->questionResultDenormalizer = $questionResultDenormalizer;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        return new QuestionAsked(
            Uuid::fromString($data['id']),
            $this->questionResultDenormalizer->denormalize(
                $data['questionResult'],
                QuestionResult::class,
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
