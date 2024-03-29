<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Forms;

use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use VSV\GVQ_API\Account\Constraints\UserIsUnique;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\Forms\CompanyFormType;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Models\TranslatedAlias;
use VSV\GVQ_API\Company\Models\TranslatedAliases;
use VSV\GVQ_API\Company\ValueObjects\Alias;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
use VSV\GVQ_API\User\Models\User;
use VSV\GVQ_API\User\ValueObjects\Email;
use VSV\GVQ_API\User\ValueObjects\Password;
use VSV\GVQ_API\User\ValueObjects\Role;

class RegistrationFormType extends CompanyFormType
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
                                'message' => $translator->trans('Field.email.pattern'),
                                'groups' => ['CorrectSyntax'],
                            ]
                        ),
                        new Length(
                            [
                                'max' => 255,
                                'maxMessage' => $translator->trans('Field.length.max'),
                                'groups' => ['CorrectSyntax'],
                            ]
                        ),
                        new NotBlank(
                            [
                                'message' => $translator->trans('Field.empty'),
                                'groups' => ['CorrectSyntax'],
                            ]
                        ),
                        new UserIsUnique(
                            [
                                'message' => $translator->trans('Field.email.in.use'),
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
                    'invalid_message' => $translator->trans('Field.passwords.different'),
                    'first_options' => [
                        'always_empty' => false,
                        'constraints' => [
                            new Regex(
                                [
                                    'pattern' => Password::PATTERN,
                                    'message' => $translator->trans('Field.password.pattern'),
                                ]
                            ),
                            new NotBlank(
                                [
                                    'message' => $translator->trans('Field.empty'),
                                ]
                            ),
                            new Length(
                                [
                                    'max' => 4096,
                                    'maxMessage' => $translator->trans('Field.length.max'),
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
                    'constraints' => $this->createCompanyNameConstraints($translator, null),
                ]
            );

        if ($options['use_company_type'] === true) {
            if ($options['use_company_type'] === true) {
                $builder->add(
                    'type',
                    ChoiceType::class,
                    [
                        'expanded' => true,
                        'multiple' => false,
                        'choices' => [
                            'een bedrijf' => 'company',
                            'een vereniging' => 'association',
                        ],
                        'required' => true,
                        'constraints' => [
                            new NotBlank(),
                        ]
                    ]
                );
            }
        }

        $builder
            ->add(
                'numberOfEmployees',
                IntegerType::class,
                [
                    'constraints' => $this->createNrOfEmployeesConstraints($translator),
                ]
            )
            ->add(
                'aliasNl',
                TextType::class,
                [
                    'constraints' => $this->createAliasConstraints($translator, null),
                ]
            )
            ->add(
                'aliasFr',
                TextType::class,
                [
                    'constraints' => $this->createAliasConstraints($translator, null),
                ]
            )
            ->add(
                'azijnpotje',
                TextType::class,
                [
                    'attr' => [
                        'autocomplete' => "off",
                    ],
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
                'use_company_type' => false,
            ]
        );
    }

    /**
     * @param UuidFactoryInterface $uuidFactory
     * @param array $data
     * @param string $language
     * @return User
     * @throws \Exception
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
     * @throws \Exception
     */
    public function createCompanyFromData(
        UuidFactoryInterface $uuidFactory,
        array $data,
        User $user
    ): Company {
        $company = new Company(
            $uuidFactory->uuid4(),
            new NotEmptyString($data['companyName']),
            new PositiveNumber($data['numberOfEmployees']),
            new TranslatedAliases(
                new TranslatedAlias(
                    $uuidFactory->uuid4(),
                    new Language(Language::NL),
                    new Alias($data['aliasNl'])
                ),
                new TranslatedAlias(
                    $uuidFactory->uuid4(),
                    new Language(Language::FR),
                    new Alias($data['aliasFr'])
                )
            ),
            $user,
            new \DateTime('now')
        );

        if (isset($data['type'])) {
            $company = $company->withType($data['type']);
        }

        return $company;
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
                    'message' => $translator->trans('Field.empty'),
                ]
            ),
            new Length(
                [
                    'max' => 255,
                    'maxMessage' => $translator->trans('Field.length.max'),
                ]
            ),
        ];
    }
}
