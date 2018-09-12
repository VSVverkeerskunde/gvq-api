<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Controllers;

use Broadway\EventSourcing\EventSourcingRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\Partner\Repositories\PartnerRepository;
use VSV\GVQ_API\Question\Repositories\AnswerRepository;
use VSV\GVQ_API\Quiz\Aggregate\QuizAggregate;
use VSV\GVQ_API\Quiz\Commands\StartQuiz;
use VSV\GVQ_API\Quiz\Repositories\QuestionResultRepository;
use VSV\GVQ_API\Quiz\Service\QuizService;
use VSV\GVQ_API\Team\Repositories\TeamRepository;

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
     * @var AnswerRepository
     */
    private $answerRepository;

    /**
     * @var QuestionResultRepository
     */
    private $questionResultRepository;

    /**
     * @var EventSourcingRepository
     */
    private $quizAggregateRepository;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param QuizService $quizService
     * @param CompanyRepository $companyRepository
     * @param PartnerRepository $partnerRepository
     * @param TeamRepository $teamRepository
     * @param AnswerRepository $answerRepository
     * @param QuestionResultRepository $questionResultRepository
     * @param EventSourcingRepository $quizAggregateRepository
     * @param SerializerInterface $serializer
     */
    public function __construct(
        QuizService $quizService,
        CompanyRepository $companyRepository,
        PartnerRepository $partnerRepository,
        TeamRepository $teamRepository,
        AnswerRepository $answerRepository,
        QuestionResultRepository $questionResultRepository,
        EventSourcingRepository $quizAggregateRepository,
        SerializerInterface $serializer
    ) {
        $this->quizService = $quizService;
        $this->companyRepository = $companyRepository;
        $this->partnerRepository = $partnerRepository;
        $this->teamRepository = $teamRepository;
        $this->answerRepository = $answerRepository;
        $this->questionResultRepository = $questionResultRepository;
        $this->quizAggregateRepository = $quizAggregateRepository;
        $this->serializer = $serializer;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function start(Request $request): Response
    {
        /** @var StartQuiz $startQuiz */
        $startQuiz = $this->serializer->deserialize(
            $request->getContent(),
            StartQuiz::class,
            'json'
        );

        $company = $startQuiz->getCompanyAlias() ?
            $this->companyRepository->getByAlias(
                $startQuiz->getCompanyAlias()
            ) : null;

        $partner = $startQuiz->getPartnerAlias() ?
            $this->partnerRepository->getByYearAndAlias(
                $this->quizService->getYear(),
                $startQuiz->getPartnerAlias()
            ) : null;

        $team = $startQuiz->getTeamId() ?
            $this->teamRepository->getByYearAndId(
                $this->quizService->getYear(),
                $startQuiz->getTeamId()
            ) : null;

        $quiz = $this->quizService->generateQuiz(
            $startQuiz->getParticipant(),
            $startQuiz->getQuizChannel(),
            $company,
            $partner,
            $team,
            $startQuiz->getLanguage()
        );

        $quizAggregate = QuizAggregate::start($quiz);
        $this->quizAggregateRepository->save($quizAggregate);

        $response = new Response('{"id":"'.$quiz->getId()->toString().'"}');
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @param string $quizId
     * @return Response
     * @throws \Exception
     */
    public function askQuestion(
        string $quizId
    ): Response {
        /** @var QuizAggregate $quizAggregate */
        $quizAggregate = $this->quizAggregateRepository->load($quizId);

        $quizAggregate->askQuestion(new \DateTimeImmutable());

        $this->quizAggregateRepository->save($quizAggregate);

        return $this->createCurrentQuestionResponse($quizId);
    }

    /**
     * @param string $quizId
     * @param string $answerId
     * @return Response
     * @throws \Exception
     */
    public function answerQuestion(
        string $quizId,
        string $answerId
    ): Response {

        if ($answerId === 'late') {
            $answer = null;
        } else {
            $answer = $this->answerRepository->getById(Uuid::fromString($answerId));
            if (null === $answer) {
                throw new \InvalidArgumentException(
                    'No answer with id "'.$answerId.'"'
                );
            }
        }

        /** @var QuizAggregate $quizAggregate */
        $quizAggregate = $this->quizAggregateRepository->load($quizId);

        $quizAggregate->answerQuestion(new \DateTimeImmutable(), $answer);

        $this->quizAggregateRepository->save($quizAggregate);

        return $this->createCurrentQuestionResponse($quizId);
    }

    /**
     * @param string $quizId
     * @return Response
     */
    private function createCurrentQuestionResponse(string $quizId): Response
    {
        $questionResult = $this->questionResultRepository->getByIdAsJson(
            Uuid::fromString($quizId)
        );

        $response = new Response($questionResult);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
