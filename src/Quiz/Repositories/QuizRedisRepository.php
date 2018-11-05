<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Repositories;

use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Common\ValueObjects\Ttl;
use VSV\GVQ_API\Quiz\Models\Quiz;

class QuizRedisRepository implements QuizRepository
{
    const KEY_PREFIX = 'quiz_';

    /**
     * @var \Redis
     */
    private $redis;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var Ttl
     */
    private $ttl;

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
        $this->ttl = new Ttl(12 * 3600);
    }

    /**
     * @param Ttl $ttl
     */
    public function updateTtl(Ttl $ttl): void
    {
        $this->ttl = $ttl;
    }

    /**
     * @inheritdoc
     */
    public function save(Quiz $quiz): void
    {
        $quizAsJson = $this->serializer->serialize($quiz, 'json');

        $this->redis->setex(
            $this->createKey($quiz->getId()),
            $this->ttl->toNative(),
            $quizAsJson
        );
    }

    /**
     * @inheritdoc
     */
    public function deleteById(UuidInterface $id): void
    {
        $this->redis->del($this->createKey($id));
    }

    /**
     * @inheritdoc
     */
    public function getById(UuidInterface $id): Quiz
    {
        $quizAsJson = $this->redis->get($this->createKey($id));

        /** @var Quiz $quiz */
        $quiz = $this->serializer->deserialize($quizAsJson, Quiz::class, 'json');
        return $quiz;
    }

    /**
     * @param UuidInterface $id
     * @return string
     */
    private function createKey(UuidInterface $id): string
    {
        return self::KEY_PREFIX.$id->toString();
    }
}
