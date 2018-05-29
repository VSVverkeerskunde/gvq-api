<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Controllers;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\Repositories\CategoryRepository;
use VSV\GVQ_API\Question\Serializers\CategoriesSerializer;

class CategoryControllerTest extends TestCase
{
    /**
     * @var CategoryRepository|MockObject
     */
    private $categoryRepository;
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

        $this->categoryController = new CategoryController(
            $this->categoryRepository,
            new CategoriesSerializer()
        );
    }

    /**
     * @test
     */
    public function it_returns_a_response(): void
    {
        $categories = ModelsFactory::createCategories();

        $this->categoryRepository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn($categories);

        $actualResponse = $this->categoryController->getAll();

        $expectedResponse = new Response(
            ModelsFactory::createJson('categories')
        );
        $expectedResponse->headers->set('Content-Type', 'application/json');

        $this->assertEquals(
            $expectedResponse->getContent(),
            $actualResponse->getContent()
        );
        $this->assertEquals(
            $expectedResponse->headers,
            $actualResponse->headers
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

        $expectedResponse = new Response('[]');
        $expectedResponse->headers->set('Content-Type', 'application/json');

        $this->assertEquals(
            $expectedResponse->getContent(),
            $actualResponse->getContent()
        );
        $this->assertEquals(
            $expectedResponse->headers->get('Content-Type'),
            $actualResponse->headers->get('Content-Type')
        );
    }
}
