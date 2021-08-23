<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Forms;

use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
                'confirm18years',
                CheckboxType::class,
                [
                    'label' => $translator->trans('Contest.18years'),
                    'required' => 'true',
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
                'answer1',
                IntegerType::class,
                [
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => $translator->trans('Field.empty'),
                            ]
                        ),
                        new Range(
                            [
                                'min' => 1,
                                'minMessage' => $translator->trans('Field.tiebreaker.min'),
                            ]
                        )
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
                        new Range(
                            [
                                'min' => 1,
                                'minMessage' => $translator->trans('Field.tiebreaker.min'),
                            ]
                        )
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

        if ($options['association']) {
            $builder->add(
                'association',
                CheckboxType::class,
                [
                    'label' => $translator->trans('Contest.association'),
                ]
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'translator' => null,
                'csrf_protection' => false,
                'association' => false,
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
        if ($channel->toNative() !== QuizChannel::LEAGUE) {
            $channel = new QuizChannel(QuizChannel::INDIVIDUAL);
        }

        return new ContestParticipation(
            $uuidFactory->uuid4(),
            $year,
            $language,
            $channel,
            new ContestParticipant(
                $email,
                new NotEmptyString($data['firstName']),
                new NotEmptyString($data['lastName']),
                // We do not ask for the birth date in edition 2021.
                null
            ),
            // We do not ask for the address in edition 2021.
            null,
            new PositiveNumber($data['answer1']),
            new PositiveNumber($data['answer2']),
            $data['gdpr1'],
            $data['gdpr2'],
            isset($data['association']) ? $data['association'] : false
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
