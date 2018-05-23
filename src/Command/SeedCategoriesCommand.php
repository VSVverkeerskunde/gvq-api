<?php declare(strict_types=1);

namespace VSV\GVQ_API\Command;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VSV\GVQ_API\Question\Models\Categories;
use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Question\Repositories\CategoryRepository;
use VSV\GVQ_API\Question\ValueObjects\NotEmptyString;

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
            ->setDescription('Upload all categories');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('Seeding categories...');

        $categories = $this->createCategories()->toArray();
        foreach ($categories as $category) {
            $output->writeln('Seeding category: '.$category->getName()->toNative());
            $this->categoriesRepository->save($category);
        }

        $output->writeln('Seeding finished.');
    }

    /**
     * @return Categories
     */
    private function createCategories(): Categories
    {
        return new Categories(
            new Category(
                Uuid::fromString('a7910bf1-05f9-4bdb-8dee-1256cbfafc0b'),
                new NotEmptyString('Algemene verkeersregels')
            ),
            new Category(
                Uuid::fromString('15530c78-2b1c-4820-bcfb-e04c5e2224b9'),
                new NotEmptyString('Kwetsbare weggebruikers')
            ),
            new Category(
                Uuid::fromString('67844067-82ca-4698-a713-b5fbd4c729c5'),
                new NotEmptyString('Verkeerstekens')
            ),
            new Category(
                Uuid::fromString('58ee6bd3-a3f4-42bc-ba81-82491cec55b9'),
                new NotEmptyString('Voorrang')
            ),
            new Category(
                Uuid::fromString('1289d4b5-e88e-4b3c-9223-eb2c7c49f4d0'),
                new NotEmptyString('EHBO/Ongeval/Verzekering')
            ),
            new Category(
                Uuid::fromString('9677995d-5fc5-48cd-a251-565b626cb7c1'),
                new NotEmptyString('Voertuig/Techniek')
            ),
            new Category(
                Uuid::fromString('fce11f3c-24ad-4aed-b00d-0069e3404749'),
                new NotEmptyString('Openbaar vervoer/Milieu')
            ),
            new Category(
                Uuid::fromString('6f0c9e04-1e84-4ba4-be54-ab5747111754'),
                new NotEmptyString('Verkeersveiligheid')
            )
        );
    }
}
