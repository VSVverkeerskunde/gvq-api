<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Forms;

use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Question\Models\Answer;
use VSV\GVQ_API\Question\Models\Answers;
use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\ValueObjects\Year;

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
        /** @var Question $question */
        $question = $options['question'];
        /** @var TranslatorInterface $translator */
        $translator = $options['translator'];

        /** @var Answer[] $answers */
        $answers = $question ? $question->getAnswers()->toArray() : null;

        $builder
            ->add(
                'language',
                ChoiceType::class,
                [
                    'label' => false,
                    'choices' => $languages,
                    'choice_label' => function (?Language $language) {
                        return $language ? $language->toNative() : '';
                    },
                    'choice_value' => function (?Language $language) {
                        return $language ? $language->toNative() : '';
                    },
                    'data' => $question ? $question->getLanguage() : null,
                ]
            )
            ->add(
                'year',
                IntegerType::class,
                [
                    'label' => false,
                    'data' => $question ? $question->getYear()->toNative() : null,
                    'required' => false,
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => $translator->trans('Het jaar mag niet leeg zijn.'),
                            ]
                        ),
                        new Range(
                            [
                                'min' => 2018,
                                'max' => 2099,
                                'minMessage' => $translator->trans('Het jaar moet {{ limit }} of groter zijn.'),
                                'maxMessage' => $translator->trans('Het jaar moet {{ limit }} of kleiner zijn.'),
                            ]
                        ),
                    ]
                ]
            )
            ->add(
                'category',
                ChoiceType::class,
                [
                    'label' => false,
                    'choices' => $categories,
                    'choice_label' => function (?Category $category) {
                        return $category ? $category->getName()->toNative() : '';
                    },
                    'choice_value' => function (?Category $category) {
                        return $category ? $category->getId()->toString() : '';
                    },
                    'data' => $question ? $question->getCategory() : null,
                ]
            )
            ->add(
                'text',
                TextareaType::class,
                [
                    'label' => false,
                    'data' => $question ? $question->getText()->toNative() : null,
                    'required' => false,
                    'constraints' => $this->createTextConstraint($translator),
                ]
            )
            ->add(
                'answer1',
                TextareaType::class,
                [
                    'label' => false,
                    'data' => $answers ? $answers[0]->getText()->toNative() : null,
                    'required' => false,
                    'constraints' => $this->createTextConstraint($translator),
                ]
            )
            ->add(
                'answer2',
                TextareaType::class,
                [
                    'label' => false,
                    'data' => $answers ? $answers[1]->getText()->toNative() : null,
                    'required' => false,
                    'constraints' => $this->createTextConstraint($translator),
                ]
            )
            ->add(
                'answer3',
                TextareaType::class,
                [
                    'label' => false,
                    'data' => $answers ? $answers[2]->getText()->toNative() : null,
                    'required' => false,
                    'constraints' => $this->createTextConstraint($translator),
                ]
            )
            ->add(
                'correctAnswer',
                ChoiceType::class,
                [
                    'label' => false,
                    'choices' => [
                        'Antwoord 1' => 0,
                        'Antwoord 2' => 1,
                        'Antwoord 3' => 2,
                    ],
                    'data' => $question ? $this->getCorrectAnswerIndex($question->getAnswers()) : null,
                ]
            )
            ->add(
                'feedback',
                TextareaType::class,
                [
                    'label' => false,
                    'data' => $question ? $question->getFeedback()->toNative() : null,
                    'required' => false,
                    'constraints' => $this->createTextConstraint($translator),
                ]
            );

        if ($question == null) {
            $builder->add(
                'image',
                FileType::class,
                [
                    'label' => false,
                ]
            );
        }
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
                'question' => null,
            ]
        );
    }

    /**
     * @param UuidFactoryInterface $uuidFactory
     * @param NotEmptyString $imageFileName
     * @param array $data
     * @return Question
     */
    public function newQuestionFromData(
        UuidFactoryInterface $uuidFactory,
        NotEmptyString $imageFileName,
        array $data
    ): Question {
        $answers = [
            new Answer(
                $uuidFactory->uuid4(),
                new NotEmptyString($data['answer1']),
                $data['correctAnswer'] === 0 ? true : false
            ),
            new Answer(
                $uuidFactory->uuid4(),
                new NotEmptyString($data['answer2']),
                $data['correctAnswer'] === 1 ? true : false
            ),
        ];

        if (!empty($data['answer3'])) {
            $answers[] = new Answer(
                $uuidFactory->uuid4(),
                new NotEmptyString($data['answer3']),
                $data['correctAnswer'] === 2 ? true : false
            );
        }

        return new Question(
            $uuidFactory->uuid4(),
            $data['language'],
            new Year($data['year']),
            $data['category'],
            new NotEmptyString($data['text']),
            $imageFileName,
            new Answers(...$answers),
            new NotEmptyString($data['feedback'])
        );
    }

    /**
     * @param Question $question
     * @param array $data
     * @return Question
     */
    public function updateQuestionFromData(
        Question $question,
        array $data
    ): Question {
        $answers = [
            new Answer(
                $question->getAnswers()->toArray()[0]->getId(),
                new NotEmptyString($data['answer1']),
                $data['correctAnswer'] === 0 ? true : false
            ),
            new Answer(
                $question->getAnswers()->toArray()[1]->getId(),
                new NotEmptyString($data['answer2']),
                $data['correctAnswer'] === 1 ? true : false
            ),
        ];

        if (!empty($data['answer3'])) {
            $answers[] = new Answer(
                $question->getAnswers()->toArray()[2]->getId(),
                new NotEmptyString($data['answer3']),
                $data['correctAnswer'] === 2 ? true : false
            );
        }

        return new Question(
            $question->getId(),
            $data['language'],
            new Year($data['year']),
            $data['category'],
            new NotEmptyString($data['text']),
            $question->getImageFileName(),
            new Answers(...$answers),
            new NotEmptyString($data['feedback'])
        );
    }

    /**
     * @param Answers $answers
     * @return int
     */
    private function getCorrectAnswerIndex(Answers $answers): int
    {
        /** @var Answer $answer */
        for ($index = 0; $index < count($answers); $index++) {
            if ($answers->toArray()[$index]->isCorrect()) {
                return $index;
            }
        }

        return 0;
    }

    /**
     * @param TranslatorInterface $translator
     * @return Constraint[]
     */
    private function createTextConstraint(TranslatorInterface $translator): array
    {
        return [
            new NotBlank(
                [
                    'message' => $translator->trans('De tekst mag niet leeg zijn.'),
                ]
            ),
            new Length(
                [
                    'max' => 1024,
                    'maxMessage' => $translator->trans('De tekst mag niet meer dan {{ limit }} karakters hebben.'),
                ]
            ),
        ];
    }
}
