<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Question\Models\Category;

class QuestionFormType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Category[] $categories */
        $categories = $options['categories']->toArray();
        /** @var Language[] $languages */
        $languages = $options['languages']->toArray();

        $builder
            ->add(
                'language',
                ChoiceType::class,
                [
                    'label' => false,
                    'choices' => $languages,
                    'choice_label' => function (Language $language) {
                        return $language->toNative();
                    }
                ]
            )
            ->add(
                'year',
                IntegerType::class,
                [
                    'label' => false,
                ]
            )
            ->add(
                'category',
                ChoiceType::class,
                [
                    'label' => false,
                    'choices' => $categories,
                    'choice_label' => function (Category $category) {
                        return $category->getName()->toNative();
                    },
                ]
            )
            ->add(
                'photo',
                FileType::class,
                [
                    'label' => false,
                ]
            )
            ->add(
                'text',
                TextareaType::class,
                [
                    'label' => false,
                ]
            )
            ->add(
                'answer1',
                TextareaType::class,
                [
                    'label' => false,
                ]
            )
            ->add(
                'answer2',
                TextareaType::class,
                [
                    'label' => false,
                ]
            )
            ->add(
                'answer3',
                TextareaType::class,
                [
                    'label' => false,
                ]
            )
            ->add(
                'correctAnswer',
                ChoiceType::class,
                [
                    'label' => false,
                    'choices' => [
                        'Antwoord 1' => 1,
                        'Antwoord 2' => 2,
                        'Antwoord 3' => 3,
                    ],
                ]
            )
            ->add(
                'feedback',
                TextareaType::class,
                [
                    'label' => false,
                ]
            );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'languages' => [],
                'categories' => [],
            ]
        );
    }
}
