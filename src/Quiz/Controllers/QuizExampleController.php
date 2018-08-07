<?php

namespace VSV\GVQ_API\Quiz\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class QuizExampleController extends AbstractController
{
    public function showQuiz(): Response {
        return new Response(
            $this->renderView('quiz/example.html.twig')
        );
    }
}
