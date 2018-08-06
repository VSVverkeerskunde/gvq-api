<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Controllers;

use Broadway\EventSourcing\EventSourcingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\Partner\Repositories\PartnerRepository;
use VSV\GVQ_API\Quiz\Aggregate\QuizAggregate;
use VSV\GVQ_API\Quiz\Service\QuizService;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
use VSV\GVQ_API\Quiz\ValueObjects\QuizParticipant;
use VSV\GVQ_API\Team\Repositories\TeamRepository;
use VSV\GVQ_API\User\ValueObjects\Email;

class QuizController
{
    /**
     * @var QuizService
     */
    private $quizService;

    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * @var PartnerRepository
     */
    private $partnerRepository;

    /**
     * @var TeamRepository
     */
    private $teamRepository;

    /**
     * @var EventSourcingRepository
     */
    private $quizAggregateRepository;

    /**
     * @param QuizService $quizService
     * @param CompanyRepository $companyRepository
     * @param PartnerRepository $partnerRepository
     * @param TeamRepository $teamRepository
     * @param EventSourcingRepository $quizAggregateRepository
     */
    public function __construct(
        QuizService $quizService,
        CompanyRepository $companyRepository,
        PartnerRepository $partnerRepository,
        TeamRepository $teamRepository,
        EventSourcingRepository $quizAggregateRepository
    ) {
        $this->quizService = $quizService;
        $this->companyRepository = $companyRepository;
        $this->partnerRepository = $partnerRepository;
        $this->teamRepository = $teamRepository;
        $this->quizAggregateRepository = $quizAggregateRepository;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function start(Request $request): Response
    {
        // TODO: Need to be created from DTO.
        $participant = new QuizParticipant(new Email('test@2dtostwice.be'));
        $quizChannel = new QuizChannel(QuizChannel::INDIVIDUAL);
        $company = null;
        $partner = null;
        $team = null;
        $language = new Language('nl');

        // TODO: To make it work questions are needed.
        $quiz = $this->quizService->generateQuiz(
            $participant,
            $quizChannel,
            $company,
            $partner,
            $team,
            $language
        );

        $quizAggregate = QuizAggregate::start($quiz);
        $this->quizAggregateRepository->save($quizAggregate);

        $response = new Response('{"id":"'.$quiz->getId()->toString().'"}');
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function askQuestion(Request $request): Response
    {
        // Return the question to ask, but no correct answer and no feedback.
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function answerQuestion(Request $request): Response
    {
        // Return the question with feedback and correct answer.
    }
}
