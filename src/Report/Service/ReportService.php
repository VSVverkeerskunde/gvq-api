<?php declare(strict_types=1);

namespace VSV\GVQ_API\Report\Service;

use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Statistics\Models\QuestionDifficulties;
use VSV\GVQ_API\Statistics\Repositories\QuestionDifficultyRepository;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;

class ReportService
{
    /**
     * @var QuestionDifficultyRepository
     */
    private $questionCorrectRepository;

    /**
     * @var QuestionDifficultyRepository
     */
    private $questionInCorrectRepository;

    /**
     * @param QuestionDifficultyRepository $questionCorrectRepository
     * @param QuestionDifficultyRepository $questionInCorrectRepository
     */
    public function __construct(
        QuestionDifficultyRepository $questionCorrectRepository,
        QuestionDifficultyRepository $questionInCorrectRepository
    ) {
        $this->questionCorrectRepository = $questionCorrectRepository;
        $this->questionInCorrectRepository = $questionInCorrectRepository;
    }

    /**
     * @param Language $language
     * @return QuestionDifficulties
     */
    public function getCorrectQuestions(Language $language): QuestionDifficulties
    {
        return $this->questionCorrectRepository->getRange(
            $language,
            new NaturalNumber(4)
        );
    }

    /**
     * @param Language $language
     * @return QuestionDifficulties
     */
    public function getInCorrectQuestions(Language $language): QuestionDifficulties
    {
        return $this->questionInCorrectRepository->getRange(
            $language,
            new NaturalNumber(4)
        );
    }
}
