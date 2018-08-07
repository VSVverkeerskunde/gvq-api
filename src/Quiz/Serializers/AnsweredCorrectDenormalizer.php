<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use VSV\GVQ_API\Quiz\Events\AnsweredCorrect;

class AnsweredCorrectDenormalizer extends AbstractAnsweredEventDenormalizer
{
    /**
     * @return string
     */
    protected function getAnsweredEventClassName(): string
    {
        return AnsweredCorrect::class;
    }
}
