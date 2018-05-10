<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Serializers;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

class QuestionSerializer extends Serializer
{
    public function __construct()
    {
        $normalizers = [
            new QuestionNormalizer(
                new CategoryNormalizer(),
                new AnswerNormalizer()
            ),
            new QuestionDenormalizer(
                new CategoryDenormalizer(),
                new AnswerDenormalizer()
            ),
        ];
        $encoders = [
            new JsonEncoder(),
        ];

        parent::__construct($normalizers, $encoders);
    }
}
