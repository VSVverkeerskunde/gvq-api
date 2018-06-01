<?php

namespace VSV\GVQ_API\Question\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\Question\Repositories\QuestionRepository;

class QuestionViewController extends AbstractController
{
    /**
     * @var QuestionRepository
     */
    private $questionRepository;

    /**
     * @param QuestionRepository $questionRepository
     */
    public function __construct(QuestionRepository $questionRepository)
    {
        $this->questionRepository = $questionRepository;
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        $questions = $this->questionRepository->getAll()->toArray();

        return $this->render(
            'question/index.html.twig',
            [
                'questions' => $questions,
            ]
        );
    }
}
