<?php

namespace VSV\GVQ_API\Question\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Repositories\QuestionRepository;
use VSV\GVQ_API\Question\Serializers\QuestionSerializer;

class QuestionController
{
    /**
     * @var QuestionSerializer
     */
    private $questionSerializer;

    /**
     * @var QuestionRepository
     */
    private $questionRepository;

    /**
     * @param QuestionSerializer $questionSerializer
     * @param QuestionRepository $questionRepository
     */
    public function __construct(
        QuestionSerializer $questionSerializer,
        QuestionRepository $questionRepository
    ) {
        $this->questionSerializer = $questionSerializer;
        $this->questionRepository = $questionRepository;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        // @todo: check for missing id
        $json = $request->getContent();

        /** @var Question $question */
        $question = $this->questionSerializer->deserialize(
            $json,
            Question::class,
            'json'
        );

        $this->questionRepository->save($question);

        return new Response('QUESTION CREATED');
    }

    public function getAll(Request $request): Response
    {
        return new Response('GETALL');
    }

    public function getById(Request $request, string $id): Response
    {
        return new Response('GETBYID');
    }
}
