<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use VSV\GVQ_API\Quiz\Events\AnsweredCorrect;

class AnsweredCorrectNormalizer extends AbstractAnsweredEventNormalizer
{
    protected function getNormalizerName(): string
    {
        return AnsweredCorrect::class;
    }
}
