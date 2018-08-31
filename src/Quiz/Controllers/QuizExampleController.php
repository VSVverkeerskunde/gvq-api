<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;
use VSV\GVQ_API\Common\ValueObjects\Language;

class QuizExampleController extends AbstractController
{
    public function showQuiz(
        Request $request,
        ContainerInterface $container
    ): Response {
        $quizConfig = array();
        $teams = Yaml::parseFile($container->getParameter('kernel.project_dir').'/config/teams.yaml');

        $formBuilder = $this->createFormBuilder();
        $formBuilder
            ->add('language', ChoiceType::class, array(
                'choices' => array(
                    Language::NL => Language::NL,
                    Language::FR => Language::FR,
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
            ->add('configure', SubmitType::class);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $quizConfig = [
                'language' => $form->get('language')->getNormData(),
                'channel' => $form->get('channel')->getNormData(),
                'company' => $form->get('company')->getNormData(),
                'partner' => $form->get('partner')->getNormData(),
                'teams' => $teams['2018'],
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
