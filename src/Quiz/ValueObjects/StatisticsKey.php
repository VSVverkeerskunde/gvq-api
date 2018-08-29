<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\ValueObjects;

use VSV\GVQ_API\Common\ValueObjects\Enumeration;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Quiz\Models\Quiz;

class StatisticsKey extends Enumeration
{
    const INDIVIDUAL_NL = 'individual_nl';
    const INDIVIDUAL_FR = 'individual_fr';
    const PARTNER_NL = 'partner_nl';
    const PARTNER_FR = 'partner_fr';
    const COMPANY_NL = 'company_nl';
    const COMPANY_FR = 'company_fr';
    const CUP_NL = 'cup_nl';
    const CUP_FR = 'cup_fr';

    /**
     * @inheritdoc
     */
    public function getAllowedValues(): array
    {
        return [
            self::INDIVIDUAL_NL,
            self::INDIVIDUAL_FR,
            self::PARTNER_NL,
            self::PARTNER_FR,
            self::COMPANY_NL,
            self::COMPANY_FR,
            self::CUP_NL,
            self::CUP_FR,
        ];
    }

    /**
     * @param Quiz $quiz
     * @return StatisticsKey
     */
    public static function createFromQuiz(Quiz $quiz): StatisticsKey
    {
        $statisticsKey = '';

        switch ($quiz->getChannel()->toNative()) {
            case QuizChannel::INDIVIDUAL:
                $statisticsKey = $quiz->getLanguage()->toNative() === Language::NL ?
                    self::INDIVIDUAL_NL : self::INDIVIDUAL_FR;
                break;
            case QuizChannel::COMPANY:
                $statisticsKey = $quiz->getLanguage()->toNative() === Language::NL ?
                    self::COMPANY_NL : self::COMPANY_FR;
                break;
            case QuizChannel::PARTNER:
                $statisticsKey = $quiz->getLanguage()->toNative() === Language::NL ?
                    self::PARTNER_NL : self::PARTNER_FR;
                break;
            case QuizChannel::CUP:
                $statisticsKey = $quiz->getLanguage()->toNative() === Language::NL ?
                    self::CUP_NL : self::CUP_FR;
                break;
        }

        return new StatisticsKey($statisticsKey);
    }

    /**
     * @return StatisticsKey[]
     */
    public static function getAllKeys(): array
    {
        return [
            new StatisticsKey(self::INDIVIDUAL_NL),
            new StatisticsKey(self::INDIVIDUAL_FR),
            new StatisticsKey(self::PARTNER_NL),
            new StatisticsKey(self::PARTNER_FR),
            new StatisticsKey(self::COMPANY_NL),
            new StatisticsKey(self::COMPANY_FR),
            new StatisticsKey(self::CUP_NL),
            new StatisticsKey(self::CUP_FR),
        ];
    }
}
