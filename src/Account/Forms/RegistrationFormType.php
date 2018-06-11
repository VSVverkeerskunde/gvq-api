<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Forms;

use PHPUnit\Framework\Constraint\IsTrue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\Expression;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use VSV\GVQ_API\Account\Constraints\FilterValidateEmail;
use VSV\GVQ_API\Account\Constraints\UserIsUnique;
use VSV\GVQ_API\User\ValueObjects\Password;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var TranslatorInterface $translator */
        $translator = $options['translator'];

        $builder
            ->add(
                'email',
                TextType::class,
                [
                    'constraints' => [
                        new FilterValidateEmail(
                            [
                                'message' => $translator->trans('Dit is geen geldig e-mailadres'),
                            ]
                        ),
                        new NotBlank(
                            [
                                'message' => $translator->trans('Het e-mailveld mag niet leeg zijn.'),
                            ]
                        ),
                        new UserIsUnique(
                            [
                                'message' => $translator->trans('Dit e-mailadres is al in gebruik.'),
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'password',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'invalid_message' => $translator->trans('De paswoorden komen niet overeen.'),
                    'help' => 'test help',
                    'first_options' => [

                        'constraints' => [
                            new Regex(
                                [
                                    'pattern' => Password::PATTERN,
                                    'message' => $translator->trans(
                                        'Het wachtwoord moet minstens 8 karakters lang zijn en minstens één kleine 
                                        letter, hoofdletter en ander karakter bevatten'
                                    ),
                                ]
                            ),
                            new NotBlank(
                                [
                                    'message' => $translator->trans('Het wachtwoord mag niet leeg zijn.'),
                                ]
                            ),
                            new Length(
                                [
                                    'max' => 4096,
                                    'maxMessage' => $translator->trans(
                                        'Het wachtwoord mag maximum {{ limit }} karakters bevatten'
                                    ),
                                ]
                            ),
                        ],
                    ],
                    'second_options' => [],
                ]
            )
            ->add(
                'name',
                TextType::class,
                [
                    'constraints' => $this->createNameConstraints($translator),
                ]
            )
            ->add(
                'firstName',
                TextType::class,
                [
                    'constraints' => $this->createNameConstraints($translator),
                ]
            )
            ->add(
                'companyName',
                TextType::class,
                [
                    'constraints' => $this->createNameConstraints($translator),
                ]
            )
            ->add(
                'numberOfEmployees',
                IntegerType::class,
                [
                    'constraints' => [
                        new GreaterThan(
                            [
                                'value' => 0,
                                'message' => $translator->trans('Het aantal werknemers moet groter zijn dan 0.'),
                            ]
                        ),
                        new NotBlank(
                            [
                                'message' => $translator->trans('Het aantal werknemers mag niet leeg zijn.'),
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'aliasNl',
                TextType::class,
                [
                    'constraints' => $this->createAliasConstraints($translator),
                ]
            )
            ->add(
                'aliasFr',
                TextType::class,
                [
                    'constraints' => $this->createAliasConstraints($translator),
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
                'translator' => null,
            ]
        );
    }

    /**
     * @param TranslatorInterface $translator
     * @return array
     */
    private function createNameConstraints(TranslatorInterface $translator): array
    {
        return [
            new NotBlank(
                [
                    'message' => $translator->trans('Dit veld mag niet leeg zijn.'),
                ]
            ),
            new Length(
                [
                    'max' => 255,
                    'maxMessage' => $translator->trans('Dit veld mag maximum {{ limit }} karakters bevatten.'),
                ]
            ),
        ];
    }

    /**
     * @param TranslatorInterface $translator
     * @return array
     */
    private function createAliasConstraints(TranslatorInterface $translator): array
    {
        return [
            new NotBlank(
                [
                    'message' => $translator->trans('Dit veld mag niet leeg zijn.'),
                ]
            ),
            new Length(
                [
                    'min' => 3,
                    'max' => 255,
                    'maxMessage' => $translator->trans('Dit veld mag maximum {{ limit }} karakters bevatten.'),
                    'minMessage' => $translator->trans('Dit veld moet minimum {{ limit }} karakters bevatten.'),
                ]
            ),
        ];
    }
}
