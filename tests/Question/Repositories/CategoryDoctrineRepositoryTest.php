<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories;

use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\Repositories\AbstractDoctrineRepositoryTest;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\Models\Categories;
use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Question\Repositories\Entities\CategoryEntity;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;

class CategoryDoctrineRepositoryTest extends AbstractDoctrineRepositoryTest
{
    /**
     * @var Category
     */
    private $category;

    /**
     * @var CategoryDoctrineRepository
     */
    private $categoryDoctrineRepository;

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->category = ModelsFactory::createAccidentCategory();

        $this->categoryDoctrineRepository = new CategoryDoctrineRepository(
            $this->entityManager
        );
    }

    /**
     * @inheritdoc
     */
    protected function getRepositoryName(): string
    {
        return CategoryEntity::class;
    }

    /**
     * @test
     */
    public function it_can_save_a_category(): void
    {
        $this->categoryDoctrineRepository->save($this->category);

        $foundCategory = $this->categoryDoctrineRepository->getById(
            Uuid::fromString('1289d4b5-e88e-4b3c-9223-eb2c7c49f4d0')
        );

        $this->assertEquals($this->category, $foundCategory);
    }

    /**
     * @test
     */
    public function it_can_update_a_category(): void
    {
        $this->categoryDoctrineRepository->save($this->category);

        $updatedCategory = new Category(
            Uuid::fromString('1289d4b5-e88e-4b3c-9223-eb2c7c49f4d0'),
            new NotEmptyString('Ongeval')
        );
        $this->categoryDoctrineRepository->update($updatedCategory);

        $foundCategory = $this->categoryDoctrineRepository->getById(
            Uuid::fromString('1289d4b5-e88e-4b3c-9223-eb2c7c49f4d0')
        );

        $this->assertEquals($updatedCategory, $foundCategory);
    }

    /**
     * @test
     */
    public function it_can_delete_a_category(): void
    {
        $this->categoryDoctrineRepository->save($this->category);

        $this->categoryDoctrineRepository->delete($this->category);

        $foundCategory = $this->categoryDoctrineRepository->getById(
            Uuid::fromString('1289d4b5-e88e-4b3c-9223-eb2c7c49f4d0')
        );

        $this->assertNull($foundCategory);
    }

    /**
     * @test
     */
    public function it_can_get_a_category_by_id(): void
    {
        $this->categoryDoctrineRepository->save($this->category);

        $foundCategory = $this->categoryDoctrineRepository->getById(
            Uuid::fromString('1289d4b5-e88e-4b3c-9223-eb2c7c49f4d0')
        );

        $this->assertEquals($this->category, $foundCategory);
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
        $this->categoryDoctrineRepository->save($this->category);

        $generalCategory = ModelsFactory::createGeneralCategory();
        $this->categoryDoctrineRepository->save($generalCategory);

        $foundCategories = $this->categoryDoctrineRepository->getAll();

        $this->assertEquals(
            new Categories($this->category, $generalCategory),
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
