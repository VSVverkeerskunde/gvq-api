<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Company\Models\Companies;
use VSV\GVQ_API\Company\Models\Company;

class CompaniesNormalizer implements NormalizerInterface
{
    /**
     * @var CompanyNormalizer
     */
    private $companyNormalizer;

    /**
     * @param CompanyNormalizer $companyNormalizer
     */
    public function __construct(CompanyNormalizer $companyNormalizer)
    {
        $this->companyNormalizer = $companyNormalizer;
    }

    /**
     * @inheritdoc
     * @param Companies $companies
     */
    public function normalize($companies, $format = null, array $context = []): array
    {
        return array_map(
            function (Company $company) use ($format, $context) {
                return $this->companyNormalizer->normalize($company, $format, $context);
            },
            $companies->toArray()
        );
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof Companies) && ($format === 'json' || $format === 'csv');
    }
}
