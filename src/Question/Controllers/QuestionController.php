<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Controllers;

use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Repositories\QuestionRepository;

class QuestionController
{
    /**
     * @var QuestionRepository
     */
    private $questionRepository;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var UuidFactoryInterface
     */
    private $uuidFactory;

    /**
     * @param QuestionRepository $questionRepository
     * @param SerializerInterface $serializer
     * @param UuidFactoryInterface $uuidFactory
     */
    public function __construct(
        QuestionRepository $questionRepository,
        SerializerInterface $serializer,
        UuidFactoryInterface $uuidFactory
    ) {
        $this->questionRepository = $questionRepository;
        $this->serializer = $serializer;
        $this->uuidFactory = $uuidFactory;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function save(Request $request): Response
    {
        $json = $request->getContent();
        /** @var Question $question */
        $question = $this->serializer->deserialize($json, Question::class, 'json');
        $this->questionRepository->save($question);

        $response = new Response('{"id":"'.$question->getId()->toString().'"}');
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @param string $id
     * @return Response
     */
    public function delete(string $id): Response
    {
        $this->questionRepository->delete(
            $this->uuidFactory->fromString($id)
        );

        $response = new Response('{"id":"'.$id.'"}');
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return Response
     */
    public function getAll(): Response
    {
        $questions = $this->questionRepository->getAll();

        if ($questions === null) {
            $response = new Response('[]');
        } else {
            $questionsAsJson = $this->serializer->serialize($questions, 'json');
            $response = new Response($questionsAsJson);
        }

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
