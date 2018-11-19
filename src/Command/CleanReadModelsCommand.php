<?php declare(strict_types=1);

namespace VSV\GVQ_API\Command;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use VSV\GVQ_API\Question\Repositories\Entities\CategoryEntity;
use VSV\GVQ_API\Statistics\Repositories\FinishedQuizRedisRepository;
use VSV\GVQ_API\Statistics\Repositories\StartedQuizRedisRepository;
use VSV\GVQ_API\Statistics\Repositories\TeamParticipationRedisRepository;
use VSV\GVQ_API\Statistics\Repositories\TeamTotalScoreRedisRepository;
use VSV\GVQ_API\Statistics\Repositories\UniqueParticipantRedisRepository;

class CleanReadModelsCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->setName('gvq:clean-read-models')
            ->setDescription('Clean all read models (both MySQL and Redis).')
            ->addOption(
                'no-mysql',
                null,
                InputOption::VALUE_NONE,
                'Do not clear MySQL read models'
            )
            ->addOption(
                'only-contest-related',
                '',
                InputOption::VALUE_NONE,
                'Only clear read models that are related to the contest, not the ones needed for quizes that are in progress'
            );
    }

    /**
     * @inheritdoc
     * @throws DBALException
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Continue with cleaning the read models? ', false);

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        $output->writeln('Start cleaning read models...');

        if (!$input->getOption('no-mysql')) {
            $tables = [
                'employee_participation',
                'team_participant',
                'top_score',
                'detailed_top_score',
            ];

            foreach ($tables as $table) {
                $output->writeln("Cleaning ${table} table ...");

                $this->entityManager->getConnection()->exec(
                    "DELETE FROM ${table};"
                );
            }
        }

        /** @var \Redis $redis */
        $redis = $this->getContainer()->get('redis_service');

        if ($input->getOption('only-contest-related')) {
            $output->writeln('Cleaning Redis selectively...');

            $prefixesOfKeysToClear = [
                StartedQuizRedisRepository::KEY_PREFIX,
                FinishedQuizRedisRepository::KEY_PREFIX,
                UniqueParticipantRedisRepository::KEY_PREFIX,
                'passed_' . UniqueParticipantRedisRepository::KEY_PREFIX,
                TeamTotalScoreRedisRepository::KEY_PREFIX,
                TeamParticipationRedisRepository::KEY_PREFIX,

                'answered_correct_',
                'answered_incorrect_',
            ];

            // Unfortunately the difficulty per category is not properly prefixed,
            // starts with category ids which are UUIDs.
            $categoryRepo = $this->entityManager->getRepository(CategoryEntity::class);

            /** @var CategoryEntity $category */
            foreach ($categoryRepo->findAll() as $category) {
                $prefixesOfKeysToClear[] = $category->getId() . '_';
            }

            foreach ($prefixesOfKeysToClear as $prefix) {
                $output->writeln('keys starting with ' . $prefix);
                foreach ($redis->keys($prefix . '*') as $key) {
                    $output->writeln($key);

                    $redis->del($key);
                }
            }
        }
        else {
            $output->writeln('Cleaning Redis...');
            $redis->flushDB();
        }

        $output->writeln('Finished cleaning read models...');
    }
}
