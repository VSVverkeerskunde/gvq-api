<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

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
                DateType::class
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
                IntegerType::class
            )
            ->add(
                'answer2',
                IntegerType::class
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
