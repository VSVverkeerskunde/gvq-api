<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Repositories\QuestionRepository;
use VSV\GVQ_API\Statistics\Models\QuestionDifficulty;
use VSV\GVQ_API\Statistics\Models\QuestionDifficulties;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;
use VSV\GVQ_API\Statistics\ValueObjects\Percentage;

class QuestionDifficultyRedisRepository extends AbstractRedisRepository implements QuestionDifficultyRepository
{
    private const ANSWERED_CORRECT = 'answered_correct';
    private const ANSWERED_INCORRECT = 'answered_incorrect';

    /**
     * @var QuestionCounterRepository
     */
    private $answeredCorrectRepository;

    /**
     * @var QuestionCounterRepository
     */
    private $answeredInCorrectRepository;

    /**
     * @var QuestionRepository
     */
    private $questionRepository;

    /**
     * @var null|NotEmptyString
     */
    private $keyPrefix;

    /**
     * @param \Redis $redis
     * @param QuestionCounterRepository $answeredCorrectRepository
     * @param QuestionCounterRepository $answeredInCorrectRepository
     * @param QuestionRepository $questionRepository
     * @param null|NotEmptyString $keyPrefix
     */
    public function __construct(
        \Redis $redis,
        QuestionCounterRepository $answeredCorrectRepository,
        QuestionCounterRepository $answeredInCorrectRepository,
        QuestionRepository $questionRepository,
        NotEmptyString $keyPrefix = null
    ) {
        parent::__construct($redis);

        $this->answeredCorrectRepository = $answeredCorrectRepository;
        $this->answeredInCorrectRepository = $answeredInCorrectRepository;
        $this->questionRepository = $questionRepository;

        $this->keyPrefix = $keyPrefix;
    }

    /**
     * @inheritdoc
     */
    public function update(Question $question): void
    {
        $answeredCorrectCount = $this->answeredCorrectRepository->getCount($question);
        $answeredInCorrectCount = $this->answeredInCorrectRepository->getCount($question);

        $divider = $answeredCorrectCount->toNative() + $answeredInCorrectCount->toNative();

        $this->add(
            $question,
            new NotEmptyString(self::ANSWERED_CORRECT),
            $answeredCorrectCount->toNative(),
            $divider
        );

        $this->add(
            $question,
            new NotEmptyString(self::ANSWERED_INCORRECT),
            $answeredInCorrectCount->toNative(),
            $divider
        );
    }

    /**
     * @param Language $language
     * @param NaturalNumber $end
     * @return QuestionDifficulties
     */
    public function getBestRange(
        Language $language,
        NaturalNumber $end
    ): QuestionDifficulties {
        return $this->getRange(
            new NotEmptyString(self::ANSWERED_CORRECT),
            $language,
            $end
        );
    }

    /**
     * @param Language $language
     * @param NaturalNumber $end
     * @return QuestionDifficulties
     */
    public function getWorstRange(
        Language $language,
        NaturalNumber $end
    ): QuestionDifficulties {
        return $this->getRange(
            new NotEmptyString(self::ANSWERED_INCORRECT),
            $language,
            $end
        );
    }

    /**
     * @param Question $question
     * @param NotEmptyString $keyPrefix
     * @param float $denominator
     * @param float $divider
     */
    private function add(
        Question $question,
        NotEmptyString $keyPrefix,
        float $denominator,
        float $divider
    ): void {
        $this->redis->zAdd(
            $this->createKey(
                $keyPrefix,
                $question->getLanguage()
            ),
            $denominator/$divider,
            $question->getId()->toString()
        );
    }

    /**
     * @param NotEmptyString $prefix
     * @param Language $language
     * @param NaturalNumber $end
     * @return QuestionDifficulties
     */
    private function getRange(
        NotEmptyString $prefix,
        Language $language,
        NaturalNumber $end
    ): QuestionDifficulties {
        $questionsAndScores = $this->redis->zRevRange(
            $this->createKey(
                $prefix,
                $language
            ),
            0,
            $end->toNative(),
            true
        );

        $questionDifficulties = [];
        foreach ($questionsAndScores as $questionId => $score) {
            $question = $this->questionRepository->getById(Uuid::fromString($questionId));
            if ($question) {
                $questionDifficulties[] = new QuestionDifficulty(
                    $question,
                    new Percentage($score)
                );
            }
        }

        return new QuestionDifficulties(...$questionDifficulties);
    }

    /**
     * @param NotEmptyString $prefix
     * @param Language $language
     * @return string
     */
    private function createKey(NotEmptyString $prefix, Language $language): string
    {
        $key = '';

        if (null !== $this->keyPrefix) {
            $key .= $this->keyPrefix->toNative() . '_';
        }

        $key .= $prefix->toNative() . '_' . $language->toNative();

        return $key;
    }
}
