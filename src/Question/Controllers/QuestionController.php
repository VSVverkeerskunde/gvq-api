<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Repositories\QuestionRepository;
use VSV\GVQ_API\Question\Serializers\QuestionSerializer;

class QuestionController
{
    /**
     * @var QuestionRepository
     */
    private $questionRepository;

    /**
     * @var QuestionSerializer
     */
    private $questionSerializer;

    /**
     * @param QuestionRepository $questionRepository
     * @param QuestionSerializer $questionSerializer
     */
    public function __construct(
        QuestionRepository $questionRepository,
        QuestionSerializer $questionSerializer
    ) {
        $this->questionRepository = $questionRepository;
        $this->questionSerializer = $questionSerializer;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function save(Request $request): Response
    {
        $json = $request->getContent();
        /** @var Question $question */
        $question = $this->questionSerializer->deserialize($json, Question::class, 'json');
        $this->questionRepository->save($question);

        $response = new Response('{"id":"'.$question->getId()->toString().'"}');
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
