<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Forms;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\User\Models\User;
use VSV\GVQ_API\User\ValueObjects\Email;
use VSV\GVQ_API\User\ValueObjects\Role;

class UserFormType extends EditContactFormType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $languages = $options['languages']->toArray();
        $roles = $options['roles']->toArray();

        $builder
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
                    'data' => $this->user ? $this->user->getLanguage() : null,
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
                    'data' => $this->user ? $this->user->getRole() : null,
                ]
            )
            ->add(
                'active',
                CheckboxType::class,
                [
                    'label' => $this->translator->trans('Active'),
                    'data' => $this->user ? $this->user->isActive() : false,
                ]
            );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(
            [
                'languages' => [],
                'roles' => [],
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
}
