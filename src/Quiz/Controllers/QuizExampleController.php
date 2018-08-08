<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class QuizExampleController extends AbstractController
{
    public function showQuiz(Request $request): Response
    {
        $quizConfig = array();

        $formBuilder = $this->createFormBuilder();
        $formBuilder
            ->add('language', ChoiceType::class, array(
                'choices' => array(
                    'nl' => 'nl',
                    'fr' => 'fr',
                ),
            ))
            ->add('channel', ChoiceType::class, array(
                'choices' => array(
                    'individual' => 'individual',
                    'company' => 'company',
                    'partner' => 'partner',
                    'cup' => 'cup',
                ),
            ))
            ->add('company', TextType::class, array(
                'required' => false,
                'empty_data' => null
            ))
            ->add('partner', TextType::class, array(
                'required' => false,
                'empty_data' => null
            ))
            ->add('team', TextType::class, array(
                'required' => false,
                'empty_data' => null
            ))
            ->add('configure', SubmitType::class);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $quizConfig = [
                'language' => $form->get('language')->getNormData(),
                'channel' => $form->get('channel')->getNormData(),
                'company' => $form->get('company')->getNormData(),
                'partner' => $form->get('partner')->getNormData(),
                'team' => $form->get('team')->getNormData(),
            ];
        }

        return new Response(
            $this->renderView('quiz/example.html.twig', array(
                'form' => $form->createView(),
                'quizConfig' => (object) $quizConfig
            ))
        );
    }
}
