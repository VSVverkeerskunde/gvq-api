<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories;

use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Question\Models\Categories;
use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Question\ValueObjects\NotEmptyString;

class CategoryDoctrineRepositoryTest extends AbstractDoctrineRepositoryTest
{
    /**
     * @var CategoryDoctrineRepository
     */
    private $categoryDoctrineRepository;

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryDoctrineRepository = new CategoryDoctrineRepository(
            $this->entityManager
        );
    }

    /**
     * @inheritdoc
     */
    protected function getRepositoryName(): string
    {
        return Category::class;
    }

    /**
     * @test
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function it_can_save_a_category(): void
    {
        $category = new Category(
            Uuid::fromString('1289d4b5-e88e-4b3c-9223-eb2c7c49f4d0'),
            new NotEmptyString('EHBO/Ongeval/Verzekering')
        );

        $this->categoryDoctrineRepository->save($category);

        $foundCategory = $this->findById(
            Uuid::fromString('1289d4b5-e88e-4b3c-9223-eb2c7c49f4d0')
        );

        $this->assertEquals($category, $foundCategory);
    }

    /**
     * @test
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function it_can_update_a_category(): void
    {
        $category = new Category(
            Uuid::fromString('1289d4b5-e88e-4b3c-9223-eb2c7c49f4d0'),
            new NotEmptyString('EHBO/Ongeval/Verzekering')
        );
        $this->save($category);

        $updatedCategory = new Category(
            Uuid::fromString('1289d4b5-e88e-4b3c-9223-eb2c7c49f4d0'),
            new NotEmptyString('Algemene verkeersregels')
        );
        $this->categoryDoctrineRepository->update($updatedCategory);

        $foundCategory = $this->findById(
            Uuid::fromString('1289d4b5-e88e-4b3c-9223-eb2c7c49f4d0')
        );

        $this->assertEquals(
            $updatedCategory,
            $foundCategory
        );
    }

    /**
     * @test
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function it_can_delete_a_category(): void
    {
        $category = new Category(
            Uuid::fromString('1289d4b5-e88e-4b3c-9223-eb2c7c49f4d0'),
            new NotEmptyString('EHBO/Ongeval/Verzekering')
        );
        $this->save($category);

        $this->categoryDoctrineRepository->delete($category);

        $foundCategory = $this->findById(
            Uuid::fromString('1289d4b5-e88e-4b3c-9223-eb2c7c49f4d0')
        );

        $this->assertNull($foundCategory);
    }

    /**
     * @test
     */
    public function it_can_get_a_category_by_id(): void
    {
        $category = new Category(
            Uuid::fromString('1289d4b5-e88e-4b3c-9223-eb2c7c49f4d0'),
            new NotEmptyString('EHBO/Ongeval/Verzekering')
        );
        $this->save($category);

        $foundCategory = $this->categoryDoctrineRepository->getById(
            Uuid::fromString('1289d4b5-e88e-4b3c-9223-eb2c7c49f4d0')
        );

        $this->assertEquals(
            $category,
            $foundCategory
        );
    }

    /**
     * @test
     */
    public function it_returns_null_when_category_not_found_by_id(): void
    {
        $foundCategory = $this->categoryDoctrineRepository->getById(
            Uuid::fromString('a7910bf1-05f9-4bdb-8dee-1256cbfafc0b')
        );

        $this->assertNull($foundCategory);
    }

    /**
     * @test
     */
    public function it_can_get_all_categories(): void
    {
        $category1 = new Category(
            Uuid::fromString('1289d4b5-e88e-4b3c-9223-eb2c7c49f4d0'),
            new NotEmptyString('EHBO/Ongeval/Verzekering')
        );
        $this->save($category1);

        $category2 = new Category(
            Uuid::fromString('a7910bf1-05f9-4bdb-8dee-1256cbfafc0b'),
            new NotEmptyString('Algemene verkeersregels')
        );
        $this->save($category2);

        $foundCategories = $this->categoryDoctrineRepository->getAll();

        $this->assertEquals(
            new Categories(
                $category1,
                $category2
            ),
            $foundCategories
        );
    }

    /**
     * @test
     */
    public function it_returns_null_when_no_categories_present(): void
    {
        $foundCategories = $this->categoryDoctrineRepository->getAll();

        $this->assertNull($foundCategories);
    }
}
