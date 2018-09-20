<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Repositories\QuestionRepository;
use VSV\GVQ_API\Statistics\Models\QuestionDifficulty;
use VSV\GVQ_API\Statistics\Models\QuestionDifficulties;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;

class QuestionDifficultyRedisRepository extends AbstractRedisRepository implements QuestionDifficultyRepository
{
    /**
     * @var NotEmptyString
     */
    private $key;

    /**
     * @var QuestionRepository
     */
    private $questionRepository;

    /**
     * @param \Redis $redis
     * @param NotEmptyString $key
     * @param QuestionRepository $questionRepository
     */
    public function __construct(
        \Redis $redis,
        NotEmptyString $key,
        QuestionRepository $questionRepository
    ) {
        parent::__construct($redis);

        $this->key = $key;
        $this->questionRepository = $questionRepository;
    }

    /**
     * @inheritdoc
     */
    public function increment(Question $question): void
    {
        $this->redis->zIncrBy(
            $this->key->toNative(),
            1.0,
            $question->getId()->toString()
        );
    }

    /**
     * @inheritdoc
     */
    public function getRange(): QuestionDifficulties
    {
        $questionsAndScores = $this->redis->zRevRange(
            $this->key->toNative(),
            0,
            4,
            true
        );

        $questionDifficulties = [];
        foreach ($questionsAndScores as $questionId => $score) {
            $question = $this->questionRepository->getById(Uuid::fromString($questionId));
            if ($question) {
                $questionDifficulties[] = new QuestionDifficulty(
                    $question,
                    new NaturalNumber((int)$score)
                );
            }
        }

        return new QuestionDifficulties(...$questionDifficulties);
    }
}
