<?php declare(strict_types=1);

namespace VSV\GVQ_API\Command;

use Broadway\EventHandling\SimpleEventBus;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use VSV\GVQ_API\Quiz\EventStore\DoctrineEventStore;

class ReplayCommand extends ContainerAwareCommand
{
    protected function configure(): void
    {
        $this->setName('gvq:replay')
            ->setDescription('Replay all current events.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Continue with replaying all current events? ', false);

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        $output->writeln('Starting replay...');
        /** @var DoctrineEventStore $doctrineEventStore */
        $doctrineEventStore = $this->getContainer()->get('doctrine_event_store');

        /** @var SimpleEventBus $simpleEventBus */
        $simpleEventBus = $this->getContainer()->get('simple_event_bus');

        $simpleEventBus->publish($doctrineEventStore->getFullDomainEventStream());
        $output->writeln('Finished replay...');
    }
}