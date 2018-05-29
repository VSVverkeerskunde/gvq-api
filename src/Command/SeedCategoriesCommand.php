<?php declare(strict_types=1);

namespace VSV\GVQ_API\Command;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Question\Models\Categories;
use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Question\Repositories\CategoryRepository;

class SeedCategoriesCommand extends Command
{
    /**
     * @var CategoryRepository
     */
    private $categoriesRepository;

    /**
     * @param CategoryRepository $categoriesRepository
     */
    public function __construct(CategoryRepository $categoriesRepository)
    {
        parent::__construct();
        $this->categoriesRepository = $categoriesRepository;
    }

    protected function configure(): void
    {
        $this->setName('gvq:seed-categories')
            ->setDescription('Upload all categories')
            ->addArgument('categories_file', InputArgument::OPTIONAL, 'Yaml file with categories');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('Seeding categories...');

        foreach ($this->getCategories($input) as $category) {
            $output->writeln('Seeding category: '.$category->getName()->toNative());
            $this->categoriesRepository->save($category);
        }

        $output->writeln('Seeding finished.');
    }

    /**
     * @param InputInterface $input
     * @return Categories
     */
    private function getCategories(InputInterface $input): Categories
    {
        $categoriesFile = $input->getArgument('categories_file');
        if (!$categoriesFile) {
            $categoriesFile = __DIR__ . '/categories.yaml';
        }

        $categoriesAsYml = Yaml::parseFile($categoriesFile);
        $categories = $this->createCategoriesFromYml($categoriesAsYml);

        return $categories;
    }

    /**
     * @param array $categoriesAsYml
     * @return Categories
     */
    private function createCategoriesFromYml(array $categoriesAsYml): Categories
    {
        return new Categories(
            ...array_map(
                function (array $categoryAsYml) {
                    return new Category(
                        Uuid::fromString($categoryAsYml['id']),
                        new NotEmptyString($categoryAsYml['name'])
                    );
                },
                $categoriesAsYml
            )
        );
    }
}
