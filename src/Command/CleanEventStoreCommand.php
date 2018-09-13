<?php declare(strict_types=1);

namespace VSV\GVQ_API\Command;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class CleanEventStoreCommand extends Command
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
        $this->setName('gvq:clean-event-store')
            ->setDescription('Clean the event store and reset index.');
    }

    /**
     * @inheritdoc
     * @throws DBALException
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Continue with cleaning the event store? ', false);

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        $output->writeln('Start cleaning event store...');
        $this->entityManager->getConnection()->exec(
            'DELETE FROM event_store; ALTER TABLE event_store AUTO_INCREMENT = 1;'
        );
        $output->writeln('Finished cleaning event store...');
    }
}
