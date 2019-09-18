<?php declare(strict_types=1);

namespace VSV\GVQ_API\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VSV\GVQ_API\Mail\Service\MailService;
use VSV\GVQ_API\Registration\Repositories\RegistrationRepository;

class SendKickOffEmailCommand extends Command
{
    /**
     * @var RegistrationRepository
     */
    private $registrationRepository;

    /**
     * @var \DateTimeImmutable
     */
    private $quizKickOffDate;

    /**
     * @var MailService
     */
    private $mailService;

    /**
     * @param RegistrationRepository $registrationRepository
     */
    public function __construct(RegistrationRepository $registrationRepository, \DateTimeImmutable $quizKickOffDate, MailService $mailService)
    {
        parent::__construct();
        $this->registrationRepository = $registrationRepository;
        $this->quizKickOffDate = $quizKickOffDate;
        $this->mailService = $mailService;
    }

    protected function configure(): void
    {
        $this
            ->setName('gvq:send-kick-off-email')
            ->setDescription('Sends the kick-off e-mail to active company responsibles who registered before the kick-off date.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): void {
        $registrations = $this->registrationRepository->getUsedRegistrationsCreatedBefore($this->quizKickOffDate);

        $msg = 'sending kick-off e-mail for ' . count($registrations) . ' registrations before ' . $this->quizKickOffDate->format('Y-m-d');
        $output->writeln($msg);

        foreach ($registrations as $registration) {
            $output->writeln(
                $registration->getCreatedOn()->format('Y-m-d') . ' ' .
                $registration->getUser()->getEmail()->toNative()
            );

            $this->mailService->sendKickOffMail($registration);
        }
    }
}
