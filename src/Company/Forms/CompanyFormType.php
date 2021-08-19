<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Forms;

use Ramsey\Uuid\UuidFactoryInterface;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Models\TranslatedAlias;
use VSV\GVQ_API\Company\Models\TranslatedAliases;
use VSV\GVQ_API\Company\ValueObjects\Alias;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
use VSV\GVQ_API\User\Models\User;

class CompanyFormType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Company $company */
        $company = $options['company'];
        /** @var TranslatorInterface $translator */
        $translator = $options['translator'];

        $builder
            ->add(
                'companyName',
                TextType::class,
                [
                    'data' => $company ? $company->getName()->toNative() : null,
                    'constraints' => $this->createCompanyNameConstraints($translator, $company),
                ]
            )
            ->add(
                'nrOfEmployees',
                IntegerType::class,
                [
                    'data' => $company ? $company->getNumberOfEmployees()->toNative() : null,
                    'constraints' => $this->createNrOfEmployeesConstraints($translator),
                ]
            );

        if ($options['use_company_type'] === true) {
            $builder->add(
                'type',
                ChoiceType::class,
                [
                    'expanded' => true,
                    'multiple' => false,
                    'data' => $company ? $company->getType() : null,
                    'choices' => [
                        'een bedrijf' => 'company',
                        'een vereniging' => 'association',
                    ],
                    'required' => true,
                ]
            );
        }

        $builder
            ->add(
                'aliasNl',
                TextType::class,
                [
                    'data' => $company ? $company
                        ->getTranslatedAliases()
                        ->getByLanguage(
                            new Language(Language::NL)
                        )
                        ->getAlias()
                        ->toNative() : null,
                    'constraints' => $this->createAliasConstraints($translator, $company),
                ]
            )
            ->add(
                'aliasFr',
                TextType::class,
                [
                    'data' => $company ? $company
                        ->getTranslatedAliases()
                        ->getByLanguage(
                            new Language(Language::FR)
                        )
                        ->getAlias()
                        ->toNative() : null,
                    'constraints' => $this->createAliasConstraints($translator, $company),
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
                'company' => null,
                'use_company_type' => false,
                'translator' => null,
            ]
        );
    }

    /**
     * @param UuidFactoryInterface $uuidFactory
     * @param array $data
     * @param User $user
     * @return Company
     * @throws \Exception
     */
    public function newCompanyFromData(
        UuidFactoryInterface $uuidFactory,
        array $data,
        User $user
    ): Company {
        return new Company(
            $uuidFactory->uuid4(),
            new NotEmptyString($data['companyName']),
            new PositiveNumber($data['nrOfEmployees']),
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
            new \DateTime()
        );
    }

    /**
     * @param Company $company
     * @param array $data
     * @return Company
     */
    public function updateCompanyFromData(
        Company $company,
        array $data
    ): Company {
        $updated_company = new Company(
            $company->getId(),
            new NotEmptyString($data['companyName']),
            new PositiveNumber($data['nrOfEmployees']),
            new TranslatedAliases(
                new TranslatedAlias(
                    $this->getTranslatedId($company, new Language(Language::NL)),
                    new Language(Language::NL),
                    new Alias($data['aliasNl'])
                ),
                new TranslatedAlias(
                    $this->getTranslatedId($company, new Language(Language::FR)),
                    new Language(Language::FR),
                    new Alias($data['aliasFr'])
                )
            ),
            $company->getUser(),
            $company->getCreated()
        );

        $updated_company = $updated_company->withType($company->getType());

        if (isset($data['type'])) {
            $updated_company = $updated_company->withType($data['type']);
        }

        return $updated_company;
    }

    /**
     * @param Company $company
     * @param Language $language
     * @return UuidInterface
     */
    private function getTranslatedId(
        Company $company,
        Language $language
    ): UuidInterface {
        $translatedAlias = $company->getTranslatedAliases()->getByLanguage($language);

        return $translatedAlias->getId();
    }

    /**
     * @param TranslatorInterface $translator
     * @param null|Company $company
     * @return array
     */
    protected function createCompanyNameConstraints(
        TranslatorInterface $translator,
        ?Company $company
    ): array {
        return [
            new NotBlank(
                [
                    'message' => $translator->trans('Field.empty'),
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
            new CompanyIsUnique(
                [
                    'message' => $translator->trans('Field.company.in.use'),
                    'companyId' => $company ? $company->getId()->toString() : null,
                ]
            ),
        ];
    }

    /**
     * @param TranslatorInterface $translator
     * @return array
     */
    protected function createNrOfEmployeesConstraints(TranslatorInterface $translator): array
    {
        return [
            new GreaterThan(
                [
                    'value' => 0,
                    'message' => $translator->trans('Field.employees.positive'),
                    'groups' => ['CorrectSyntax'],
                ]
            ),
            new NotBlank(
                [
                    'message' => $translator->trans('Field.empty'),
                    'groups' => ['CorrectSyntax'],
                ]
            ),
        ];
    }

    /**
     * @param TranslatorInterface $translator
     * @param Company|null $company
     * @return array
     */
    protected function createAliasConstraints(
        TranslatorInterface $translator,
        ?Company $company
    ): array {
        return [
            new NotBlank(
                [
                    'message' => $translator->trans('Field.empty'),
                    'groups' => ['CorrectSyntax'],
                ]
            ),
            new Regex(
                [
                    'pattern' => Alias::PATTERN,
                    'message' => $translator->trans('Field.alias.pattern'),
                    'groups' => ['CorrectSyntax'],
                ]
            ),
            new AliasIsUnique(
                [
                    'message' => $translator->trans('Field.alias.in.use'),
                    'companyId' => $company ? $company->getId()->toString() : null,
                ]
            ),
        ];
    }
}
