<?php declare(strict_types=1);

namespace VSV\GVQ_API\Command;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VSV\GVQ_API\Question\Models\Categories;
use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Question\Repositories\CategoryRepository;
use VSV\GVQ_API\Question\ValueObjects\NotEmptyString;

class SeedCategoriesCommandTest extends TestCase
{
    /**
     * @var CategoryRepository|MockObject
     */
    private $categoriesRepository;

    /**
     * @var SeedCategoriesCommand
     */
    private $seedCategoriesCommand;

    /**
     * @throws \ReflectionException
     */
    protected function setUp(): void
    {
        $this->categoriesRepository = $this->createMock(
            CategoryRepository::class
        );

        $this->seedCategoriesCommand = new SeedCategoriesCommand(
            $this->categoriesRepository
        );
    }

    /**
     * @test
     */
    public function it_has_a_name()
    {
        $this->assertEquals(
            'gvq:seed-categories',
            $this->seedCategoriesCommand->getName()
        );
    }

    /**
     * @test
     */
    public function it_has_a_description()
    {
        $this->assertEquals(
            'Upload all categories',
            $this->seedCategoriesCommand->getDescription()
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_seeds_categories()
    {
        $this->categoriesRepository->expects($this->exactly(8))
            ->method('save');

        $this->seedCategoriesCommand->run(
            $this->createMock(InputInterface::class),
            $this->createMock(OutputInterface::class)
        );
    }
}
