<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Company\ValueObjects\Alias;
use VSV\GVQ_API\Quiz\Commands\StartQuiz;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
use VSV\GVQ_API\Quiz\ValueObjects\QuizParticipant;
use VSV\GVQ_API\User\ValueObjects\Email;

class StartQuizDenormalizer implements DenormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = array()): StartQuiz
    {
        return new StartQuiz(
            new QuizParticipant(new Email($data['email'])),
            new QuizChannel($data['channel']),
            $data['company'] ? new Alias(strtolower($data['company'])) : null,
            $data['partner'] ? new Alias(strtolower($data['partner'])) : null,
            $data['team'] ? Uuid::fromString($data['team']) : null,
            new Language($data['language'])
        );
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return ($type === StartQuiz::class) && ($format === 'json');
    }
}
