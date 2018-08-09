<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Repositories;

use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Quiz\ValueObjects\QuestionResult;

class QuestionResultRedisRepository implements QuestionResultRepository
{
    const KEY_PREFIX = 'current_question_result_';

    /**
     * @var \Redis
     */
    private $redis;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param \Redis $redis
     * @param SerializerInterface $serializer
     */
    public function __construct(
        \Redis $redis,
        SerializerInterface $serializer
    ) {
        $this->redis = $redis;
        $this->serializer = $serializer;
    }

    /**
     * @inheritdoc
     */
    public function save(UuidInterface $quizId, QuestionResult $questionResult, array $context = []): void
    {
        $questionResultAsJson = $this->serializer->serialize($questionResult, 'json', $context);

        $this->redis->set($this->createKey($quizId), $questionResultAsJson);
    }

    /**
     * @inheritdoc
     */
    public function getById(UuidInterface $quizId): QuestionResult
    {
        $questionResultAsJson = $this->redis->get($this->createKey($quizId));

        /** @var QuestionResult $questionResult */
        $questionResult = $this->serializer->deserialize($questionResultAsJson, QuestionResult::class, 'json');
        return $questionResult;
    }

    /**
     * @inheritdoc
     */
    public function getByIdAsJson(UuidInterface $quizId): string
    {
        return $this->redis->get($this->createKey($quizId));
    }

    /**
     * @param UuidInterface $quizId
     * @return string
     */
    private function createKey(UuidInterface $quizId): string
    {
        return self::KEY_PREFIX.$quizId->toString();
    }
}
