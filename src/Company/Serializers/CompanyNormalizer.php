<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Models\TranslatedAlias;
use VSV\GVQ_API\User\Serializers\UserNormalizer;

class CompanyNormalizer implements NormalizerInterface
{
    /**
     * @var TranslatedAliasNormalizer
     */
    private $translatedAliasNormalizer;

    /**
     * @var UserNormalizer
     */
    private $userNormalizer;

    /**
     * @param TranslatedAliasNormalizer $translatedAliasNormalizer
     * @param UserNormalizer $userNormalizer
     */
    public function __construct(
        TranslatedAliasNormalizer $translatedAliasNormalizer,
        UserNormalizer $userNormalizer
    ) {
        $this->translatedAliasNormalizer = $translatedAliasNormalizer;
        $this->userNormalizer = $userNormalizer;
    }

    /**
     * @inheritdoc
     * @param Company $company
     */
    public function normalize($company, $format = null, array $context = array()): array
    {
        $aliases = array_map(
            function (TranslatedAlias $translatedAlias) use ($format) {
                return $this->translatedAliasNormalizer->normalize(
                    $translatedAlias,
                    $format
                );
            },
            $company->getTranslatedAliases()->toArray()
        );

        $user = $this->userNormalizer->normalize(
            $company->getUser(),
            $format
        );

        $normalizedCompany = [
            'id' => $company->getId()->toString(),
            'name' => $company->getName()->toNative(),
            'numberOfEmployees' => $company->getNumberOfEmployees()->toNative(),
            'type' => $company->getType(),
            'aliases' => $aliases,
            'user' => $user,
            'created' => $company->getCreated()->format(\DateTime::ISO8601),
        ];

        if ($company->getNrOfPassedEmployees()) {
            $normalizedCompany['nrOfPassedEmployees'] = $company->getNrOfPassedEmployees()->toNative();
        }

        return $normalizedCompany;
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof Company) && in_array($format, ['json', 'csv', NULL]);
    }
}
