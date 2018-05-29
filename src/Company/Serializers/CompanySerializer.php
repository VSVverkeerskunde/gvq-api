<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Serializers;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use VSV\GVQ_API\User\Serializers\UserDenormalizer;
use VSV\GVQ_API\User\Serializers\UserNormalizer;

class CompanySerializer extends Serializer
{
    public function __construct()
    {
        $normalizers = [
            new CompanyNormalizer(
                new TranslatedAliasNormalizer(),
                new UserNormalizer()
            ),
            new CompanyDenormalizer(
                new TranslatedAliasDenormalizer(),
                new UserDenormalizer()
            ),
        ];
        $encoders = [
            new JsonEncoder(),
        ];

        parent::__construct($normalizers, $encoders);
    }
}
