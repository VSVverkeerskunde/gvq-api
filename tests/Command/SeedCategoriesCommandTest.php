<?php declare(strict_types=1);

namespace VSV\GVQ_API\Command;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VSV\GVQ_API\Question\Repositories\CategoryRepository;

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

    protected function setUp(): void
    {
        /** @var CategoryRepository|MockObject $categoriesRepository */
        $categoriesRepository = $this->createMock(CategoryRepository::class);
        $this->categoriesRepository = $categoriesRepository;

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

        /** @var InputInterface|MockObject $inputInterface */
        $inputInterface = $this->createMock(InputInterface::class);
        /** @var OutputInterface|MockObject $outputInterface */
        $outputInterface = $this->createMock(OutputInterface::class);

        $this->seedCategoriesCommand->run($inputInterface, $outputInterface);
    }
}
