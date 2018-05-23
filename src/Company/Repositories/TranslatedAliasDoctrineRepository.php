<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepository;
use VSV\GVQ_API\Company\Models\TranslatedAlias;
use VSV\GVQ_API\Company\Repositories\Entities\TranslatedAliasEntity;

class TranslatedAliasDoctrineRepository extends AbstractDoctrineRepository implements TranslatedAliasRepository
{
    /**
     * @inheritdoc
     */
    public function getRepositoryName(): string
    {
        return TranslatedAliasEntity::class;
    }

    /**
     * @param TranslatedAlias $translatedAlias
     */
    public function save(TranslatedAlias $translatedAlias): void
    {
        $this->entityManager->persist(
            TranslatedAliasEntity::fromTranslatedAlias($translatedAlias)
        );
        $this->entityManager->flush();
    }

    /**
     * @param UuidInterface $id
     * @return TranslatedAlias|null
     */
    public function getById(UuidInterface $id): ?TranslatedAlias
    {
        /** @var TranslatedAliasEntity|null $translatedAliasEntity */
        $translatedAliasEntity = $this->objectRepository->findOneBy(
            [
                'id' => $id,
            ]
        );

        return $translatedAliasEntity ? $translatedAliasEntity->toTranslatedAlias() : null;
    }
}
