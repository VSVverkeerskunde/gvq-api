<?php declare(strict_types=1);

namespace VSV\GVQ_API\Command;

use Broadway\Domain\DomainEventStream;
use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\SimpleEventBus;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use VSV\GVQ_API\Quiz\Events\QuizFinished;
use VSV\GVQ_API\Quiz\EventStore\DoctrineEventStore;
use VSV\GVQ_API\Quiz\Repositories\QuestionResultRedisRepository;
use VSV\GVQ_API\Quiz\Repositories\QuizRedisRepository;

class ReplayCommand extends ContainerAwareCommand
{
    protected function configure(): void
    {
        $this->setName('gvq:replay')
            ->setDescription('Replay all current events.');

        $this->addOption(
            'projector',
            'p',
            InputOption::VALUE_OPTIONAL,
            'Pass the projector to replay (all|unique)',
            'all'
        );
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
        $output->writeln('Option projector: '.$input->getOption('projector'));
        if ($input->getOption('projector') === 'unique') {
            /** @var SimpleEventBus $simpleEventBus */
            $simpleEventBus = $this->getContainer()->get('simple_unique_replay_event_bus');
        }

        /** @var QuizRedisRepository $quizRedisRepository */
        $quizRedisRepository = $this->getContainer()->get('quiz_redis_repository');

        /** @var QuestionResultRedisRepository $questionResultRedisRepository */
        $questionResultRedisRepository = $this->getContainer()->get('question_result_redis_repository');

        /** @var DomainMessage $domainMessage */
        $index = 0;
        foreach ($doctrineEventStore->getTraversableDomainMessages() as $domainMessage) {
            $output->writeln(
                $index++.' - ' .$domainMessage->getId()
                .' - '.$domainMessage->getRecordedOn()->toString()
                .' - '.$domainMessage->getType()
            );
            $simpleEventBus->publish(new DomainEventStream(array($domainMessage)));

            if ($domainMessage->getPayload() instanceof QuizFinished) {
                $output->writeln('--- Deleting quiz and question result projection...');
                /** @var QuizFinished $quizFinished */
                $quizFinished = $domainMessage->getPayload();
                $quizRedisRepository->deleteById($quizFinished->getId());
                $questionResultRedisRepository->deleteById($quizFinished->getId());
            }
        }

        $output->writeln('Finished replay...');
    }
}
