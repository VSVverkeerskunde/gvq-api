<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Controllers;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\Repositories\CategoryRepository;

class CategoryControllerTest extends TestCase
{
    /**
     * @var CategoryRepository|MockObject
     */
    private $categoryRepository;

    /**
     * @var SerializerInterface|MockObject
     */
    private $serializer;

    /**
     * @var CategoryController
     */
    private $categoryController;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        /** @var CategoryRepository|MockObject $categoryRepository */
        $categoryRepository = $this->createMock(CategoryRepository::class);
        $this->categoryRepository = $categoryRepository;

        /** @var SerializerInterface|MockObject $serializer */
        $serializer = $this->createMock(SerializerInterface::class);
        $this->serializer = $serializer;

        $this->categoryController = new CategoryController(
            $this->categoryRepository,
            $this->serializer
        );
    }

    /**
     * @test
     */
    public function it_returns_a_response(): void
    {
        $categories = ModelsFactory::createCategories();
        $categoriesJson = ModelsFactory::createJson('categories');

        $this->categoryRepository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn($categories);

        $this->serializer
            ->expects($this->once())
            ->method('serialize')
            ->with(
                $categories,
                'json'
            )
            ->willReturn($categoriesJson);

        $actualResponse = $this->categoryController->getAll();

        $this->assertEquals(
            $categoriesJson,
            $actualResponse->getContent()
        );
        $this->assertEquals(
            'application/json',
            $actualResponse->headers->get('Content-Type')
        );
    }

    /**
     * @test
     */
    public function it_returns_an_empty_array_when_no_categories_found(): void
    {
        $this->categoryRepository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn(null);

        $actualResponse = $this->categoryController->getAll();

        $this->assertEquals(
            '[]',
            $actualResponse->getContent()
        );
        $this->assertEquals(
            'application/json',
            $actualResponse->headers->get('Content-Type')
        );
    }
}
