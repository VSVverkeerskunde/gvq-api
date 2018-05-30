<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Serializers;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Models\TranslatedAlias;
use VSV\GVQ_API\Company\Models\TranslatedAliases;
use VSV\GVQ_API\User\Models\User;
use VSV\GVQ_API\User\Serializers\UserDenormalizer;

class CompanyDenormalizer implements DenormalizerInterface
{
    /**
     * @var TranslatedAliasDenormalizer
     */
    private $translatedAliasDenormalizer;

    /**
     * @var UserDenormalizer
     */
    private $userDenormalizer;

    /**
     * @param TranslatedAliasDenormalizer $translatedAliasDenormalizer
     * @param UserDenormalizer $userDenormalizer
     */
    public function __construct(
        TranslatedAliasDenormalizer $translatedAliasDenormalizer,
        UserDenormalizer $userDenormalizer
    ) {
        $this->translatedAliasDenormalizer = $translatedAliasDenormalizer;
        $this->userDenormalizer = $userDenormalizer;
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

        $user = $this->userDenormalizer->denormalize(
            $data['user'],
            User::class,
            $format,
            $context
        );

        return new Company(
            Uuid::fromString($data['id']),
            new NotEmptyString($data['name']),
            new TranslatedAliases(...$translatedAliases),
            $user
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
