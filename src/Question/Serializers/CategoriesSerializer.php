<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Serializers;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

class CategoriesSerializer extends Serializer
{
    public function __construct()
    {
        $normalizers = [
            new CategoriesNormalizer(
                new CategoryNormalizer()
            ),
        ];
        $encoders = [
            new JsonEncoder(),
        ];

        parent::__construct($normalizers, $encoders);
    }
}
