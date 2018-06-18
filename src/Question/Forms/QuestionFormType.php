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
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
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
                    ],
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
                    'constraints' => $this->createTextConstraint($translator),
                ]
            )
            ->add(
                'answer1',
                TextareaType::class,
                [
                    'label' => false,
                    'data' => $answers ? $answers[0]->getText()->toNative() : null,
                    'constraints' => $this->createTextConstraint($translator),
                ]
            )
            ->add(
                'answer2',
                TextareaType::class,
                [
                    'label' => false,
                    'data' => $answers ? $answers[1]->getText()->toNative() : null,
                    'constraints' => $this->createTextConstraint($translator),
                ]
            )
            ->add(
                'answer3',
                TextareaType::class,
                [
                    'label' => false,
                    'data' => $answers && count($answers) === 3 ? $answers[2]->getText()->toNative() : null,
                    'constraints' => $this->createTextConstraint($translator, true),
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
                    'data' => $question ? $this->getCorrectAnswerIndex($question->getAnswers()) : null,
                ]
            )
            ->add(
                'feedback',
                TextareaType::class,
                [
                    'label' => false,
                    'data' => $question ? $question->getFeedback()->toNative() : null,
                    'constraints' => $this->createTextConstraint($translator),
                ]
            );

        if ($question == null) {
            $builder->add(
                'image',
                FileType::class,
                [
                    'label' => false,
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => $translator->trans('Foto mag niet leeg zijn.'),
                            ]
                        ),
                    ],
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
                'translator' => null,
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
                new PositiveNumber(1),
                new NotEmptyString($data['answer1']),
                $data['correctAnswer'] === 1 ? true : false
            ),
            new Answer(
                $uuidFactory->uuid4(),
                new PositiveNumber(2),
                new NotEmptyString($data['answer2']),
                $data['correctAnswer'] === 2 ? true : false
            ),
        ];

        if (!empty($data['answer3'])) {
            $answers[] = new Answer(
                $uuidFactory->uuid4(),
                new PositiveNumber(3),
                new NotEmptyString($data['answer3']),
                $data['correctAnswer'] === 3 ? true : false
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
     * @param UuidFactoryInterface $uuidFactory
     * @return Question
     */
    public function updateQuestionFromData(
        Question $question,
        array $data,
        UuidFactoryInterface $uuidFactory
    ): Question {
        $answers = [
            new Answer(
                $question->getAnswers()->toArray()[0]->getId(),
                new PositiveNumber(1),
                new NotEmptyString($data['answer1']),
                $data['correctAnswer'] === 1 ? true : false
            ),
            new Answer(
                $question->getAnswers()->toArray()[1]->getId(),
                new PositiveNumber(2),
                new NotEmptyString($data['answer2']),
                $data['correctAnswer'] === 2 ? true : false
            ),
        ];

        if (!empty($data['answer3'])) {
            // if there is an existing third answer update this answer
            // else make a new one
            if (array_key_exists(2, $question->getAnswers()->toArray())) {
                $id = $question->getAnswers()->toArray()[2]->getId();
            } else {
                $id = $uuidFactory->uuid4();
            }
            $answers[] = new Answer(
                $id,
                new PositiveNumber(3),
                new NotEmptyString($data['answer3']),
                $data['correctAnswer'] === 3 ? true : false
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
        foreach ($answers as $answer) {
            if ($answer->isCorrect()) {
                return $answer->getIndex()->toNative();
            }
        }

        return 1;
    }

    /**
     * @param TranslatorInterface $translator
     * @param bool $allowEmpty
     * @return Constraint[]
     */
    private function createTextConstraint(
        TranslatorInterface $translator,
        bool $allowEmpty = false
    ): array {
        $constraints = [
            new Length(
                [
                    'max' => 1024,
                    'maxMessage' => $translator->trans('De tekst mag niet meer dan {{ limit }} karakters hebben.'),
                ]
            ),
        ];

        if (!$allowEmpty) {
            $constraints[] = new NotBlank(
                [
                    'message' => $translator->trans('De tekst mag niet leeg zijn.'),
                ]
            );
        }

        return $constraints;
    }
}
