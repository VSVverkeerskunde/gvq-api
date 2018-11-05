<?php declare(strict_types=1);

namespace VSV\GVQ_API\Command;

use Broadway\Domain\DomainMessage;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VSV\GVQ_API\Quiz\Events\QuizFinished;
use VSV\GVQ_API\Quiz\Events\QuizStarted;
use VSV\GVQ_API\Quiz\EventStore\DoctrineEventStore;
use VSV\GVQ_API\Quiz\EventStore\EventEntity;

class ListCommand extends ContainerAwareCommand
{
    protected function configure(): void
    {
        $this
            ->setName('gvq:list')
            ->setDescription('Generate a list based on event store data.')
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
            ->addArgument(
                'company-id',
                InputArgument::REQUIRED
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): void {
        $output->writeln('Start searching the event store...');
        /** @var DoctrineEventStore $doctrineEventStore */
        $doctrineEventStore = $this->getContainer()
            ->get('doctrine_event_store');

        $list = [];
        $types = [
            'VSV.GVQ_API.Quiz.Events.QuizStarted',
            'VSV.GVQ_API.Quiz.Events.QuizFinished',
        ];

        $eventEntityFeedback = function (EventEntity $event) use ($output) {
            $id = $event->getId();
            if ($id) {
                $output->writeln((string)$id);
            }
        };

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

        $companyId = $input->getArgument('company-id');
        $output->writeln('looking for participants in company with id: ' . $companyId);

        $domainMessages = $doctrineEventStore->getTraversableDomainMessages(
            $types,
            $firstId,
            $lastId,
            $eventEntityFeedback
        );

        /** @var DomainMessage $domainMessage */
        foreach ($domainMessages as $domainMessage) {
            $output->writeln(
                $domainMessage->getId() . ':' . $domainMessage->getPlayhead() . ' ' . $domainMessage->getType()
            );
            $event = $domainMessage->getPayload();
            if ($event instanceof QuizStarted) {
                $company = $event->getQuiz()->getCompany();
                if (!$company) {
                    continue;
                }

                if ($company->getId()->toString() !== $companyId) {
                    continue;
                }

                $list[$event->getId()->toString()] = [
                    'email' => $event->getQuiz()->getParticipant()->getEmail()->toNative(),
                    'score' => '!!! not finished !!!',
                ];

                $output->writeln(
                    $event->getQuiz()->getParticipant()->getEmail()->toNative()
                );
            } elseif ($event instanceof QuizFinished) {
                if (isset($list[$event->getId()->toString()])) {
                    $list[$event->getId()->toString()]['score'] = $event->getScore();
                }
            }
        }

        foreach ($list as $item) {
            $output->writeln($item['email'] . ',' . $item['score']);
        }
    }
}
