<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Serializers;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

class UserSerializer extends Serializer
{
    public function __construct()
    {
        $normalizers = [
            new UserNormalizer(),
            new UserDenormalizer(),
        ];
        $encoders = [
            new JsonEncoder(),
        ];

        parent::__construct($normalizers, $encoders);
    }
}
