<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Forms;

use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
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
use VSV\GVQ_API\Account\Constraints\AliasIsUnique;
use VSV\GVQ_API\Account\Constraints\CompanyIsUnique;
use VSV\GVQ_API\Account\Constraints\UserIsUnique;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Models\TranslatedAlias;
use VSV\GVQ_API\Company\Models\TranslatedAliases;
use VSV\GVQ_API\Company\ValueObjects\Alias;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
use VSV\GVQ_API\Registration\Models\Registration;
use VSV\GVQ_API\Registration\ValueObjects\UrlSuffixGenerator;
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
                EmailType::class,
                [
                    'constraints' => [
                        new Regex(
                            [
                                'pattern' => Email::PATTERN,
                                'message' => $translator->trans('Invalid email pattern'),
                                'groups' => ['CorrectSyntax'],
                            ]
                        ),
                        new Length(
                            [
                                'max' => 255,
                                'maxMessage' => $translator->trans('Invalid max length'),
                                'groups' => ['CorrectSyntax'],
                            ]
                        ),
                        new NotBlank(
                            [
                                'message' => $translator->trans('Empty field'),
                                'groups' => ['CorrectSyntax'],
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
                    'first_options' => [
                        'always_empty' => false,
                        'constraints' => [
                            new Regex(
                                [
                                    'pattern' => Password::PATTERN,
                                    'message' => $translator->trans('Invalid password pattern'),
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
                                    'maxMessage' => $translator->trans('Invalid max length'),
                                ]
                            ),
                        ],
                    ],
                    'second_options' => [
                        'always_empty' => false,
                    ],
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
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => $translator->trans('Empty field'),
                                'groups' => ['CorrectSyntax'],
                            ]
                        ),
                        new Length(
                            [
                                'max' => 255,
                                'maxMessage' => $translator->trans('Invalid max length'),
                                'groups' => ['CorrectSyntax'],
                            ]
                        ),
                        new CompanyIsUnique(
                            [
                                'message' => $translator->trans('Company name in use'),
                            ]
                        ),
                    ],
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
     * @param array $data
     * @param string $language
     * @return User
     */
    public function createUserFromData(
        UuidFactoryInterface $uuidFactory,
        array $data,
        string $language
    ): User {
        $user = new User(
            $uuidFactory->uuid4(),
            new Email($data['email']),
            new NotEmptyString($data['name']),
            new NotEmptyString($data['firstName']),
            new Role('contact'),
            new Language($language),
            false
        );

        $user = $user->withPassword(
            Password::fromPlainText(($data['password']))
        );

        return $user;
    }

    /**
     * @param UuidFactoryInterface $uuidFactory
     * @param array $data
     * @param User $user
     * @return Company
     */
    public function createCompanyFromData(
        UuidFactoryInterface $uuidFactory,
        array $data,
        User $user
    ): Company {
        return new Company(
            $uuidFactory->uuid4(),
            new NotEmptyString($data['companyName']),
            new PositiveNumber($data['numberOfEmployees']),
            new TranslatedAliases(
                new TranslatedAlias(
                    $uuidFactory->uuid4(),
                    new Language('nl'),
                    new Alias($data['aliasNl'])
                ),
                new TranslatedAlias(
                    $uuidFactory->uuid4(),
                    new Language('fr'),
                    new Alias($data['aliasFr'])
                )
            ),
            $user
        );
    }

    /**
     * @param UuidFactoryInterface $uuidFactory
     * @param UrlSuffixGenerator $urlSuffixGenerator
     * @param User $user
     * @param bool $passwordReset
     * @return Registration
     * @throws \Exception
     */
    public function createRegistrationForUser(
        UuidFactoryInterface $uuidFactory,
        UrlSuffixGenerator $urlSuffixGenerator,
        User $user,
        bool $passwordReset
    ): Registration {
        return new Registration(
            $uuidFactory->uuid4(),
            $urlSuffixGenerator->createUrlSuffix(),
            $user,
            new \DateTimeImmutable(),
            $passwordReset
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
                    'groups' => ['CorrectSyntax'],
                ]
            ),
            new Regex(
                [
                    'pattern' => Alias::PATTERN,
                    'message' => $translator->trans('Invalid alias pattern'),
                    'groups' => ['CorrectSyntax'],
                ]
            ),
            new AliasIsUnique(
                [
                    'message' => $translator->trans('Alias in use'),
                ]
            ),
        ];
    }
}
