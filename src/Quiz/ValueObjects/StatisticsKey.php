<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\ValueObjects;

use VSV\GVQ_API\Common\ValueObjects\Enumeration;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Quiz\Models\Quiz;

class StatisticsKey extends Enumeration
{
    const INDIVIDUAL_NL = 'individual_nl';
    const INDIVIDUAL_FR = 'individual_fr';
    const INDIVIDUAL_TOT = 'individual_total';
    const PARTNER_NL = 'partner_nl';
    const PARTNER_FR = 'partner_fr';
    const PARTNER_TOT = 'partner_total';
    const COMPANY_NL = 'company_nl';
    const COMPANY_FR = 'company_fr';
    const COMPANY_TOT = 'company_total';
    const QUIZ_NL_TOT = 'quiz_total_nl';
    const QUIZ_FR_TOT = 'quiz_total_fr';
    const QUIZ_TOT = 'quiz_total';
    const CUP_NL = 'cup_nl';
    const CUP_FR = 'cup_fr';
    const CUP_TOT = 'cup_total';
    const OVERALL_NL = 'total_nl';
    const OVERALL_FR = 'total_fr';
    const OVERALL_TOT = 'total';

    /**
     * @inheritdoc
     */
    public function getAllowedValues(): array
    {
        return [
            self::INDIVIDUAL_NL,
            self::INDIVIDUAL_FR,
            self::INDIVIDUAL_TOT,
            self::PARTNER_NL,
            self::PARTNER_FR,
            self::PARTNER_TOT,
            self::COMPANY_NL,
            self::COMPANY_FR,
            self::COMPANY_TOT,
            self::QUIZ_NL_TOT,
            self::QUIZ_FR_TOT,
            self::QUIZ_TOT,
            self::CUP_NL,
            self::CUP_FR,
            self::CUP_TOT,
            self::OVERALL_NL,
            self::OVERALL_FR,
            self::OVERALL_TOT,
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
     * @param Quiz $quiz
     * @return StatisticsKey
     */
    public static function createChannelTotalFromQuiz(Quiz $quiz): StatisticsKey
    {
        $statisticsKey = '';

        switch ($quiz->getChannel()->toNative()) {
            case QuizChannel::INDIVIDUAL:
                $statisticsKey = self::INDIVIDUAL_TOT;
                break;
            case QuizChannel::COMPANY:
                $statisticsKey = self::COMPANY_TOT;
                break;
            case QuizChannel::PARTNER:
                $statisticsKey = self::PARTNER_TOT;
                break;
            case QuizChannel::CUP:
                $statisticsKey = self::CUP_TOT;
                break;
        }

        return new StatisticsKey($statisticsKey);
    }

    /**
     * @param Quiz $quiz
     * @return StatisticsKey
     */
    public static function createQuizTotalFromQuiz(Quiz $quiz): StatisticsKey
    {
        if ($quiz->getChannel()->equals(new QuizChannel(QuizChannel::CUP))) {
            throw new\InvalidArgumentException(
                'Cup does not count in quiz total.'
            );
        }

        if ($quiz->getLanguage()->equals(new Language(Language::NL))) {
            $statisticsKey = self::QUIZ_NL_TOT;
        } else {
            $statisticsKey = self::QUIZ_FR_TOT;
        }

        return new StatisticsKey($statisticsKey);
    }

    /**
     * @param Quiz $quiz
     * @return StatisticsKey
     */
    public static function createOverallTotalFromQuiz(Quiz $quiz): StatisticsKey
    {
        if ($quiz->getLanguage()->equals(new Language(Language::NL))) {
            $statisticsKey = self::OVERALL_NL;
        } else {
            $statisticsKey = self::OVERALL_FR;
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

    /**
     * @return Language
     */
    public function getLanguage(): Language
    {
        return new Language(substr($this->toNative(), -2));
    }

    /**
     * @return QuizChannel
     */
    public function getChannel(): QuizChannel
    {
        return new QuizChannel(
            substr(
                $this->toNative(),
                0,
                strpos($this->toNative(), '_')
            )
        );
    }
}
