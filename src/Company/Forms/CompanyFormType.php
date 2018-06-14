<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Forms;

use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use VSV\GVQ_API\Account\Constraints\AliasIsUnique;
use VSV\GVQ_API\Account\Constraints\CompanyIsUnique;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Models\TranslatedAlias;
use VSV\GVQ_API\Company\Models\TranslatedAliases;
use VSV\GVQ_API\Company\ValueObjects\Alias;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;

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

        // TODO: Add constraints!
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'data' => $company ? $company->getName()->toNative() : null,
                    'constraints' => [
                        new CompanyIsUnique(
                            [
                                'message' => $translator->trans('Company name in use'),
                                'companyId' => $company ? $company->getId()->toString() : null,
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'nrOfEmployees',
                IntegerType::class,
                [
                    'data' => $company ? $company->getNumberOfEmployees()->toNative() : null,
                ]
            )
            ->add(
                'aliasNl',
                TextType::class,
                [
                    'data' => $company ? $company
                        ->getTranslatedAliases()
                        ->getByLanguage(
                            new Language('nl')
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
                            new Language('fr')
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
                'translator' => null,
            ]
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

        return new Company(
            $company->getId(),
            new NotEmptyString($data['name']),
            new PositiveNumber($data['nrOfEmployees']),
            new TranslatedAliases(
                new TranslatedAlias(
                    $this->getTranslatedId($company, new Language('nl')),
                    new Language('nl'),
                    new Alias($data['aliasNl'])
                ),
                new TranslatedAlias(
                    $this->getTranslatedId($company, new Language('fr')),
                    new Language('fr'),
                    new Alias($data['aliasFr'])
                )
            ),
            $company->getUser()
        );
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
     * @param Company|null $company
     * @return array
     */
    private function createAliasConstraints(TranslatorInterface $translator, ?Company $company): array
    {
        return [
            new AliasIsUnique(
                [
                    'message' => $translator->trans('Alias in use'),
                    'companyId' => $company ? $company->getId()->toString() : null,
                ]
            ),
        ];
    }
}
