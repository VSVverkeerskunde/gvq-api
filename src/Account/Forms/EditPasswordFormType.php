<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use VSV\GVQ_API\User\Models\User;
use VSV\GVQ_API\User\ValueObjects\Password;

class EditPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var TranslatorInterface $translator */
        $translator = $options['translator'];

        $builder
            ->add(
                'oldPassword',
                PasswordType::class,
                [
                    'constraints' => [
                        new UserPassword(
                            [
                                'message' => $translator->trans('Field.password.old.incorrect'),
                            ]
                        ),
                    ],
                    'always_empty' => false,
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
     * @param User $user
     * @param array $data
     * @return User
     */
    public function editUserPassword(User $user, array $data): User
    {
        $user = $user->withPassword(
            Password::fromPlainText($data['password'])
        );

        return $user;
    }
}
