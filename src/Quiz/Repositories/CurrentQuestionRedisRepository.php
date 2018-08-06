<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Repositories;

use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Question\Models\Question;

class CurrentQuestionRedisRepository implements CurrentQuestionRepository
{
    const KEY_PREFIX = 'current_question_';

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
    public function save(UuidInterface $quizId, Question $question): void
    {
        $questionAsJson = $this->serializer->serialize($question, 'json');

        $this->redis->set($this->createKey($quizId), $questionAsJson);
    }

    /**
     * @inheritdoc
     */
    public function getById(UuidInterface $quizId): Question
    {
        $questionAsJson = $this->redis->get($this->createKey($quizId));

        /** @var Question $question */
        $question = $this->serializer->deserialize($questionAsJson, Question::class, 'json');
        return $question;
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
