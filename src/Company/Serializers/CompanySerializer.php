<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Serializers;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

class CompanySerializer extends Serializer
{
    public function __construct()
    {
        $normalizers = [
            new CompanyNormalizer(
                new TranslatedAliasNormalizer()
            ),
            new CompanyDenormalizer(
                new TranslatedAliasDenormalizer()
            ),
        ];
        $encoders = [
            new JsonEncoder(),
        ];

        parent::__construct($normalizers, $encoders);
    }
}
