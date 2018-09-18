<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Forms;

use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
use VSV\GVQ_API\Contest\Models\ContestParticipation;
use VSV\GVQ_API\Contest\ValueObjects\Address;
use VSV\GVQ_API\Contest\ValueObjects\ContestParticipant;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
use VSV\GVQ_API\User\ValueObjects\Email;

class ContestFormType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var TranslatorInterface $translator */
        $translator = $options['translator'];

        $builder
            ->add(
                'firstName',
                TextType::class,
                [
                    'constraints' => $this->createTextConstraint($translator),
                ]
            )
            ->add(
                'lastName',
                TextType::class,
                [
                    'constraints' => $this->createTextConstraint($translator),
                ]
            )
            ->add(
                'dateOfBirth',
                BirthdayType::class,
                [
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => $translator->trans('Field.empty'),
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'street',
                TextType::class,
                [
                    'constraints' => $this->createTextConstraint($translator),
                ]
            )
            ->add(
                'number',
                TextType::class,
                [
                    'constraints' => $this->createTextConstraint($translator),
                ]
            )
            ->add(
                'postalCode',
                TextType::class,
                [
                    'constraints' => $this->createTextConstraint($translator),
                ]
            )
            ->add(
                'town',
                TextType::class,
                [
                    'constraints' => $this->createTextConstraint($translator),
                ]
            )
            ->add(
                'answer1',
                IntegerType::class,
                [
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => $translator->trans('Field.empty'),
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'answer2',
                IntegerType::class,
                [
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => $translator->trans('Field.empty'),
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'gdpr1',
                CheckboxType::class,
                [
                    'label' => $translator->trans('Contest.gdpr1'),
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => $translator->trans('Field.empty'),
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'gdpr2',
                CheckboxType::class,
                [
                    'label' => $translator->trans('Contest.gdpr2'),
                ]
            );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'translator' => null,
            ]
        );
    }

    /**
     * @param UuidFactoryInterface $uuidFactory
     * @param Year $year
     * @param Language $language
     * @param QuizChannel $channel
     * @param Email $email
     * @param array $data
     * @return ContestParticipation
     * @throws \Exception
     */
    public function newContestParticipationFromData(
        UuidFactoryInterface $uuidFactory,
        Year $year,
        Language $language,
        QuizChannel $channel,
        Email $email,
        array $data
    ): ContestParticipation {
        return new ContestParticipation(
            $uuidFactory->uuid4(),
            $year,
            $language,
            $channel,
            new ContestParticipant(
                $email,
                new NotEmptyString($data['firstName']),
                new NotEmptyString($data['lastName']),
                \DateTimeImmutable::createFromMutable($data['dateOfBirth'])
            ),
            new Address(
                new NotEmptyString($data['street']),
                new NotEmptyString($data['number']),
                new NotEmptyString($data['postalCode']),
                new NotEmptyString($data['town'])
            ),
            new PositiveNumber($data['answer1']),
            new PositiveNumber($data['answer2']),
            $data['gdpr1'],
            $data['gdpr2']
        );
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
                    'message' => $translator->trans('Field.empty'),
                ]
            ),
            new Length(
                [
                    'max' => 255,
                    'maxMessage' => $translator->trans('Field.text.empty'),
                ]
            ),
        ];
    }
}
