<?php declare(strict_types=1);

namespace VSV\GVQ_API\Command;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

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
        $this->setName('gvq:clean-read-models')
            ->setDescription('Clean all read models (both MySQL and Redis).');
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

        $output->writeln('Cleaning employee_participation...');
        $this->entityManager->getConnection()->exec(
            'DELETE FROM employee_participation;'
        );

        $output->writeln('Cleaning top_score table...');
        $this->entityManager->getConnection()->exec(
            'DELETE FROM top_score;'
        );

        $output->writeln('Cleaning detailed_top_score table...');
        $this->entityManager->getConnection()->exec(
            'DELETE FROM detailed_top_score;'
        );

        $output->writeln('Cleaning Redis...');
        /** @var \Redis $redis */
        $redis = $this->getContainer()->get('redis_service');
        $redis->flushDB();

        $output->writeln('Finished cleaning read models...');
    }
}
