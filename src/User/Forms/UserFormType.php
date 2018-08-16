<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use VSV\GVQ_API\Account\Constraints\UserIsUnique;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\User\Models\User;
use VSV\GVQ_API\User\ValueObjects\Email;
use VSV\GVQ_API\User\ValueObjects\Role;

class UserFormType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Language[] $languages */
        $languages = $options['languages']->toArray();
        /** @var Role[] $roles */
        $roles = $options['roles']->toArray();
        /** @var User $user */
        $user = $options['user'];
        /** @var TranslatorInterface $translator */
        $translator = $options['translator'];

        $builder
            ->add(
                'email',
                EmailType::class,
                [
                    'data' => $user ? $user->getEmail()->toNative() : null,
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
                                'userId' => $user ? $user->getId()->toString() : null,
                            ]
                        ),
                    ]
                ]
            )
            ->add(
                'firstName',
                TextType::class,
                [
                    'data' => $user ? $user->getFirstName()->toNative() : null,
                    'constraints' => $this->createNameConstraint($translator),
                ]
            )
            ->add(
                'lastName',
                TextType::class,
                [
                    'data' => $user ? $user->getLastName()->toNative() : null,
                    'constraints' => $this->createNameConstraint($translator),
                ]
            )
            ->add(
                'language',
                ChoiceType::class,
                [
                    'choices' => $languages,
                    'choice_label' => function (?Language $language) {
                        return $language ? $language->toNative() : '';
                    },
                    'choice_value' => function (?Language $language) {
                        return $language ? $language->toNative() : '';
                    },
                    'data' => $user ? $user->getLanguage() : null,
                ]
            )
            ->add(
                'role',
                ChoiceType::class,
                [
                    'disabled' => true,
                    'choices' => $roles,
                    'choice_label' => function (?Role $role) {
                        return $role ? $role->toNative() : '';
                    },
                    'choice_value' => function (?Role $role) {
                        return $role ? $role->toNative() : '';
                    },
                    'data' => $user ? $user->getRole() : null,
                ]
            )
            ->add(
                'active',
                CheckboxType::class,
                [
                    'label' => $translator->trans('Active'),
                    'data' => $user ? $user->isActive() : false,
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
                'languages' => [],
                'roles' => [],
                'user' => null,
                'translator' => null,
            ]
        );
    }

    /**
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateUserFromData(
        User $user,
        array $data
    ): User {
        return new User(
            $user->getId(),
            new Email($data['email']),
            new NotEmptyString($data['firstName']),
            new NotEmptyString($data['lastName']),
            $user->getRole(),
            $data['language'],
            $data['active']
        );
    }

    /**
     * @param TranslatorInterface $translator
     * @return Constraint[]
     */
    private function createNameConstraint(TranslatorInterface $translator): array
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
