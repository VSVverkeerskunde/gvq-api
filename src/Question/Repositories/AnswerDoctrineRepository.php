<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;
use VSV\GVQ_API\Question\Models\Answer;
use VSV\GVQ_API\Question\Repositories\Entities\AnswerEntity;

class AnswerDoctrineRepository extends AbstractDoctrineRepository implements AnswerRepository
{
    /**
     * @inheritdoc
     */
    protected function getRepositoryName(): string
    {
        return AnswerEntity::class;
    }

    /**
     * @inheritdoc
     */
    public function getById(UuidInterface $id): ?Answer
    {
        /** @var AnswerEntity|null $answerEntity */
        $answerEntity = $this->objectRepository->findOneBy(
            [
                'id' => $id,
            ]
        );

        return $answerEntity ? $answerEntity->toAnswer() : null;
    }
}
