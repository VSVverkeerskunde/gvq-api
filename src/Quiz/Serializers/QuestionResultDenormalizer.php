<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use http\Exception\InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Serializers\QuestionDenormalizer;
use VSV\GVQ_API\Quiz\Events\QuestionAsked;
use VSV\GVQ_API\Quiz\ValueObjects\QuestionResult;

class QuestionResultDenormalizer implements DenormalizerInterface
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
        return new QuestionResult(
            $this->questionDenormalizer->denormalize(
                $data['question'],
                Question::class,
                'json'
            ),
            $data['answeredTooLate'],
            new PositiveNumber($data['score'])
        );
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return ($type === QuestionResult::class) && ($format === 'json');
    }
}
