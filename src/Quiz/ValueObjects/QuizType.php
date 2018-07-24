<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\ValueObjects;

use VSV\GVQ_API\Common\ValueObjects\Enumeration;

class QuizType extends Enumeration
{
    const QUIZ = 'quiz';
    const CUP = 'cup';

    /**
     * @inheritdoc
     */
    public function getAllowedValues(): array
    {
        return [
            self::QUIZ,
            self::CUP,
        ];
    }
}
