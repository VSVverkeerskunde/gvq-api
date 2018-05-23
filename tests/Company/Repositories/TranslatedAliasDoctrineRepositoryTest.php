<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Repositories;

use PhpParser\Node\Expr\AssignOp\Mod;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepositoryTest;
use VSV\GVQ_API\Company\Repositories\Entities\TranslatedAliasEntity;
use VSV\GVQ_API\Factory\ModelsFactory;

class TranslatedAliasDoctrineRepositoryTest extends AbstractDoctrineRepositoryTest
{
    /**
     * @var TranslatedAliasDoctrineRepository
     */
    private $translatedAliasDoctrineRepository;

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->translatedAliasDoctrineRepository = new TranslatedAliasDoctrineRepository(
            $this->entityManager
        );
    }

    /**
     * @inheritdoc
     */
    protected function getRepositoryName(): string
    {
        return TranslatedAliasEntity::class;
    }

    /**
     * @test
     */
    public function it_can_save_a_translated_alias(): void
    {
        $translatedAlias = ModelsFactory::createNlAlias();

        $this->translatedAliasDoctrineRepository->save($translatedAlias);

        $foundTranslatedAlias = $this->translatedAliasDoctrineRepository->getById(
            Uuid::fromString('827a7945-ffd0-433e-b843-721c98ab72b8')
        );

        $this->assertEquals(
            $translatedAlias,
            $foundTranslatedAlias
        );
    }
}
