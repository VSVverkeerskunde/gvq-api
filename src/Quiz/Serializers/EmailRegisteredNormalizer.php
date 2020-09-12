<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Quiz\Events\EmailRegistered;
use VSV\GVQ_API\Quiz\Events\QuizFinished;

class EmailRegisteredNormalizer implements NormalizerInterface
{
    /**
     * @inheritdoc
     * @param EmailRegistered $emailRegistered
     */
    public function normalize($emailRegistered, $format = null, array $context = [])
    {
        return [
            'id' => $emailRegistered->getId()->toString(),
            'email' => $emailRegistered->getEmail()->toNative(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof EmailRegistered) && ($format === 'json');
    }
}
