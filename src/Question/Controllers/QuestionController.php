<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Controllers;

use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use VSV\GVQ_API\Common\Serializers\JsonEnricher;
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
     * @var JsonEnricher
     */
    private $jsonEnricher;

    /**
     * @param QuestionRepository $questionRepository
     * @param SerializerInterface $serializer
     * @param UuidFactoryInterface $uuidFactory
     * @param JsonEnricher $jsonEnricher
     */
    public function __construct(
        QuestionRepository $questionRepository,
        SerializerInterface $serializer,
        UuidFactoryInterface $uuidFactory,
        JsonEnricher $jsonEnricher
    ) {
        $this->questionRepository = $questionRepository;
        $this->serializer = $serializer;
        $this->uuidFactory = $uuidFactory;
        $this->jsonEnricher = $jsonEnricher;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function save(Request $request): Response
    {
        /** @var string $json */
        $json = $request->getContent();
        $json = $this->jsonEnricher->enrich($json);

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
