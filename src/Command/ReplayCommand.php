<?php declare(strict_types=1);

namespace VSV\GVQ_API\Command;

use Broadway\Domain\DomainEventStream;
use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\SimpleEventBus;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use VSV\GVQ_API\Common\ValueObjects\Ttl;
use VSV\GVQ_API\Quiz\Events\QuizFinished;
use VSV\GVQ_API\Quiz\EventStore\DoctrineEventStore;
use VSV\GVQ_API\Quiz\EventStore\EventEntity;
use VSV\GVQ_API\Quiz\Repositories\QuestionResultRedisRepository;
use VSV\GVQ_API\Quiz\Repositories\QuizRedisRepository;

class ReplayCommand extends ContainerAwareCommand
{
    protected function configure(): void
    {
        $this
            ->setName('gvq:replay')
            ->setDescription('Replay all current events.')
            ->addOption(
                'projector',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Pass the projector to replay (all|unique|all-redis|contest-closed|team-participant)',
                'all'
            )
            ->addOption(
                'first-id',
                null,
                InputOption::VALUE_REQUIRED,
                '',
                null
            )
            ->addOption(
                'last-id',
                null,
                InputOption::VALUE_REQUIRED,
                '',
                null
            )
            ->addOption(
                'ttl',
                't',
                InputOption::VALUE_REQUIRED,
                'Pass the ttl in seconds'
            )
            ->addOption(
                'uuid-start',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'one or more start letters of the uuid'
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $firstId = $input->getOption('first-id');
        if (null !== $firstId) {
            $firstId = (int) $firstId;

            $output->writeln('from first id: ' . $firstId);
        }

        $lastId = $input->getOption('last-id');
        if (null !== $lastId) {
            $lastId = (int) $lastId;

            $output->writeln('to last id: ' . $lastId);
        }

        $doctrineEventStore = $this->getDoctrineEventStore();
        $simpleEventBus = $this->getEventBus($input);

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Continue with replaying all current events? ', true);

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        $output->writeln('Starting replay...');

        $quizRedisRepository = $this->getQuizRedisRepository($input);
        $questionResultRedisRepository = $this->getQuestionResultRedisRepository($input);

        $index = 0;

        $eventEntityFeedback = function (EventEntity $event) use ($output) {
            $id = $event->getId();
            if ($id) {
                $output->writeln((string)$id);
            }
        };

        $uuidStarts = $input->getOption('uuid-start');

        if ($uuidStarts) {
            foreach ($uuidStarts as $uuidStart) {
                $output->writeln('partition with uuids starting with ' . $uuidStart);

                /** @var DomainMessage[] $domainMessages */
                $domainMessages = $doctrineEventStore->getTraversableDomainMessages(
                    [],
                    $firstId,
                    $lastId,
                    $eventEntityFeedback,
                    $uuidStart
                );

                foreach ($domainMessages as $domainMessage) {
                    $output->writeln(
                        $index++.' - ' .$domainMessage->getId()
                        .' - '.$domainMessage->getRecordedOn()->toString()
                        .' - '.$domainMessage->getType()
                    );
                    $simpleEventBus->publish(new DomainEventStream(array($domainMessage)));

                    if ($domainMessage->getPayload() instanceof QuizFinished) {
                        /** @var QuizFinished $quizFinished */
                        $quizFinished = $domainMessage->getPayload();
                        $quizRedisRepository->deleteById($quizFinished->getId());
                        $questionResultRedisRepository->deleteById($quizFinished->getId());
                    }
                }
            }
        }
        else {
            /** @var DomainMessage[] $domainMessages */
            $domainMessages = $doctrineEventStore->getTraversableDomainMessages(
                [],
                $firstId,
                $lastId,
                $eventEntityFeedback
            );

            foreach ($domainMessages as $domainMessage) {
                $output->writeln(
                    $index++.' - ' .$domainMessage->getId()
                    .' - '.$domainMessage->getRecordedOn()->toString()
                    .' - '.$domainMessage->getType()
                );
                $simpleEventBus->publish(new DomainEventStream(array($domainMessage)));

                if ($domainMessage->getPayload() instanceof QuizFinished) {
                    /** @var QuizFinished $quizFinished */
                    $quizFinished = $domainMessage->getPayload();
                    $quizRedisRepository->deleteById($quizFinished->getId());
                    $questionResultRedisRepository->deleteById($quizFinished->getId());
                }
            }
        }

        $output->writeln('Finished replay...');
    }

    /**
     * @return DoctrineEventStore
     */
    private function getDoctrineEventStore(): DoctrineEventStore
    {
        /** @var DoctrineEventStore $doctrineEventStore */
        $doctrineEventStore = $this->getContainer()->get('doctrine_event_store');
        return $doctrineEventStore;
    }

    /**
     * @param InputInterface $input
     * @return QuestionResultRedisRepository
     */
    private function getQuestionResultRedisRepository(InputInterface $input): QuestionResultRedisRepository
    {
        /** @var QuestionResultRedisRepository $questionResultRedisRepository */
        $questionResultRedisRepository = $this->getContainer()->get('question_result_redis_repository');

        if ($this->getTtl($input)) {
            $questionResultRedisRepository->updateTtl($this->getTtl($input));
        }

        return $questionResultRedisRepository;
    }

    /**
     * @param InputInterface $input
     * @return QuizRedisRepository
     */
    private function getQuizRedisRepository(InputInterface $input): QuizRedisRepository
    {
        /** @var QuizRedisRepository $quizRedisRepository */
        $quizRedisRepository = $this->getContainer()->get('quiz_redis_repository');

        if ($this->getTtl($input)) {
            $quizRedisRepository->updateTtl($this->getTtl($input));
        }

        return $quizRedisRepository;
    }

    /**
     * @param InputInterface $input
     * @return SimpleEventBus
     */
    private function getEventBus(InputInterface $input): SimpleEventBus
    {
        $name = $input->getOption('projector');

        $eventBus = null;

        switch ($name) {
            case 'unique':
                $eventBus = $this->getContainer()->get('simple_unique_replay_event_bus');
                break;
            case 'all':
                $eventBus = $this->getContainer()->get('simple_event_bus');
                break;
            case 'all-redis':
                $eventBus = $this->getContainer()->get('all_redis_event_bus');
                break;
            case 'contest-closed':
                $eventBus = $this->getContainer()->get('contest_closed_event_bus');
                break;
            case 'team-participant':
                $eventBus = $this->getContainer()->get('team_participant_replay_event_bus');
                break;
            default:
        }

        if (!$eventBus) {
            throw new InvalidArgumentException('invalid event bus specified: ' . $name);
        }

        return $eventBus;
    }

    /**
     * @param InputInterface $input
     * @return null|Ttl
     */
    private function getTtl(InputInterface $input): ?Ttl
    {
        if (!empty($input->getOption('ttl'))) {
            return new Ttl((int)$input->getOption('ttl'));
        }

        return null;
    }
}
