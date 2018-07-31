<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Controllers;

use Broadway\EventSourcing\EventSourcingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Quiz\Aggregate\QuizAggregate;

class QuizController
{
    /**
     * @var EventSourcingRepository
     */
    private $quizAggregateRepository;

    /**
     * @param EventSourcingRepository $quizAggregateRepository
     */
    public function __construct(EventSourcingRepository $quizAggregateRepository)
    {
        $this->quizAggregateRepository = $quizAggregateRepository;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function start(Request $request): Response
    {
        // For now hardcoded quiz object needs to be created from DTO with
        //  - Email
        //  - Channel
        //  - Language
        //  - Company id
        //  - Partner id
        //  - Team id
        $quiz = ModelsFactory::createIndividualQuiz();

        $quizAggregate = QuizAggregate::start($quiz);
        $this->quizAggregateRepository->save($quizAggregate);

        $response = new Response('{"id":"'.$quiz->getId()->toString().'"}');
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
