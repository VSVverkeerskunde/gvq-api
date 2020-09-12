<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Controllers;

use Broadway\EventSourcing\EventSourcingRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\Partner\Repositories\PartnerRepository;
use VSV\GVQ_API\Question\Repositories\AnswerRepository;
use VSV\GVQ_API\Quiz\Aggregate\QuizAggregate;
use VSV\GVQ_API\Quiz\Commands\StartQuiz;
use VSV\GVQ_API\Quiz\Repositories\QuestionResultRepository;
use VSV\GVQ_API\Quiz\Service\QuizService;
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
        try {
            /** @var StartQuiz $startQuiz */
            $startQuiz = $this->serializer->deserialize(
                $request->getContent(),
                StartQuiz::class,
                'json'
            );
        } catch (\InvalidArgumentException $exception) {
            return new Response(
                'Invalid company alias found. Please check the provided company alias inside the link.',
                Response::HTTP_NOT_FOUND
            );
        }

        try {
            $company = $this->guardedGetCompany($startQuiz);
        } catch (\InvalidArgumentException $exception) {
            return new Response(
                $exception->getMessage(),
                Response::HTTP_NOT_FOUND
            );
        }

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
            $startQuiz->getQuizChannel(),
            $company,
            $partner,
            $team,
            $startQuiz->getLanguage(),
            $startQuiz->getFirstQuestionId()
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
        $quizAggregate = $this->safeLoadQuizAggregate($quizId);

        if ($quizAggregate === null) {
            return new Response(
                'No quiz with id '.$quizId.'.',
                Response::HTTP_NOT_FOUND
            );
        }

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

        $quizAggregate = $this->safeLoadQuizAggregate($quizId);

        if ($quizAggregate === null) {
            return new Response(
                'No quiz with id '.$quizId.'.',
                Response::HTTP_NOT_FOUND
            );
        }

        $quizAggregate->answerQuestion(new \DateTimeImmutable(), $answer);

        $this->quizAggregateRepository->save($quizAggregate);

        return $this->createCurrentQuestionResponse($quizId);
    }

    /**
     * @param string $quizId
     * @param string $email
     * @return Response
     * @throws \Exception
     */
    public function registerEmail(
        string $quizId,
        string $email
    ): Response {
        $quizAggregate = $this->safeLoadQuizAggregate($quizId);

        if ($quizAggregate === null) {
            return new Response(
                'No quiz with id '.$quizId.'.',
                Response::HTTP_NOT_FOUND
            );
        }

        $quizAggregate->registerEmail(new Email($email));

        $this->quizAggregateRepository->save($quizAggregate);

        return new Response();
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

    /**
     * @param StartQuiz $startQuiz
     * @return null|Company
     */
    private function guardedGetCompany(StartQuiz $startQuiz): ?Company
    {
        $companyAlias = $startQuiz->getCompanyAlias();

        if ($companyAlias === null) {
            return null;
        }

        $company = $this->companyRepository->getByAlias($companyAlias);
        if ($company === null) {
            throw new \InvalidArgumentException(
                'No company found for alias "'.$companyAlias->toNative().'".'
                .' Please check the provided company alias inside the link.'
            );
        }

        return $company;
    }

    /**
     * @param string $quizId
     * @return QuizAggregate|null
     */
    private function safeLoadQuizAggregate(
        string $quizId
    ): ?QuizAggregate {
        try {
            /** @var QuizAggregate|null $quizAggregate */
            $quizAggregate = $this->quizAggregateRepository->load($quizId);

            return $quizAggregate;
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }
}
