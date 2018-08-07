<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class LoginFormType extends AbstractType
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
                'email',
                EmailType::class,
                [
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => $translator->trans('Field.empty'),
                            ]
                        ),
                        new Email(
                            [
                                'message' => $translator->trans('Field.email.pattern'),
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'password',
                PasswordType::class,
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
                'userName',
                TextType::class,
                [
                    'attr' => [
                        'autocomplete' => "null",
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
            ]
        );
    }
}
