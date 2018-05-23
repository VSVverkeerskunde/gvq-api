<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Serializers;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Company\Models\TranslatedAlias;
use VSV\GVQ_API\Company\ValueObjects\Alias;

class TranslatedAliasDenormalizer implements DenormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = array()): TranslatedAlias
    {
        // TODO: Better to use decorator and inject uuid generator.
        if (!isset($data['id'])) {
            $data['id'] = Uuid::uuid4();
        }

        return new TranslatedAlias(
            Uuid::fromString($data['id']),
            new Language($data['language']),
            new Alias($data['alias'])
        );
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return ($type === TranslatedAlias::class) && ($format === 'json');
    }
}
