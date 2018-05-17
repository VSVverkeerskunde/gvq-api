<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Serializers;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Models\TranslatedAlias;
use VSV\GVQ_API\Company\Models\TranslatedAliases;

class CompanyDenormalizer implements DenormalizerInterface
{
    /**
     * @var TranslatedAliasDenormalizer
     */
    private $translatedAliasDenormalizer;

    /**
     * @param TranslatedAliasDenormalizer $translatedAliasDenormalizer
     */
    public function __construct(TranslatedAliasDenormalizer $translatedAliasDenormalizer)
    {
        $this->translatedAliasDenormalizer = $translatedAliasDenormalizer;
    }

    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = array()): Company
    {
        $translatedAliases = array_map(
            function (array $translatedAlias) use ($format) {
                return $this->translatedAliasDenormalizer->denormalize(
                    $translatedAlias,
                    TranslatedAlias::class,
                    $format
                );
            },
            $data['aliases']
        );

        return new Company(
            Uuid::fromString($data['id']),
            new NotEmptyString($data['name']),
            new TranslatedAliases(...$translatedAliases)
        );
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return ($type === Company::class) && ($format === 'json');
    }
}
