<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Forms;

use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use VSV\GVQ_API\Account\Constraints\UserIsUnique;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\User\Models\User;
use VSV\GVQ_API\User\ValueObjects\Password;
use VSV\GVQ_API\User\ValueObjects\Role;
use VSV\GVQ_API\User\ValueObjects\Email;

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
                        new Regex(
                            [
                                'pattern' => Email::PATTERN,
                                'message' => $translator->trans('Invalid email pattern'),
                                'groups' => ['First'],
                            ]
                        ),
                        new Length(
                            [
                                'max' => 255,
                                'maxMessage' => $translator->trans('Invalid max length'),
                                'groups' => ['First'],
                            ]
                        ),
                        new NotBlank(
                            [
                                'message' => $translator->trans('Empty field'),
                                'groups' => ['First'],
                            ]
                        ),
                        new UserIsUnique(
                            [
                                'message' => $translator->trans('Email in use'),
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
                    'invalid_message' => $translator->trans('Different passwords'),
                    'help' => 'test help',
                    'first_options' => [
                        'constraints' => [
                            new Regex(
                                [
                                    'pattern' => Password::PATTERN,
                                    'message' => $translator->trans(
                                        'Invalid password pattern'
                                    ),
                                ]
                            ),
                            new NotBlank(
                                [
                                    'message' => $translator->trans('Empty field'),
                                ]
                            ),
                            new Length(
                                [
                                    'max' => 4096,
                                    'maxMessage' => $translator->trans(
                                        'Invalid max length'
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
                                'message' => $translator->trans('Invalid number of employees'),
                            ]
                        ),
                        new NotBlank(
                            [
                                'message' => $translator->trans('Empty field'),
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
                    'message' => $translator->trans('Empty field'),
                ]
            ),
            new Length(
                [
                    'max' => 255,
                    'maxMessage' => $translator->trans('Invalid max length'),
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
                    'message' => $translator->trans('Empty field'),
                ]
            ),
            new Length(
                [
                    'min' => 3,
                    'max' => 255,
                    'maxMessage' => $translator->trans('Invalid max length'),
                    'minMessage' => $translator->trans('Invalid min length'),
                ]
            ),
        ];
    }

    /**
     * @param UuidFactoryInterface $uuidFactory
     * @param array $data
     * @return User
     */
    public function createUserFromData(UuidFactoryInterface $uuidFactory, array $data): User
    {
        $user = new User(
            $uuidFactory->uuid4(),
            new Email($data['email']),
            new NotEmptyString($data['name']),
            new NotEmptyString($data['firstName']),
            new Role('vsv'),
            new Language('nl'),
            false
        );

        return $user;
    }
}
