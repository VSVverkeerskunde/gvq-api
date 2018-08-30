<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Forms;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use VSV\GVQ_API\User\Models\User;
use VSV\GVQ_API\User\ValueObjects\Password;

class EditPasswordFormType extends PasswordFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(
                'oldPassword',
                PasswordType::class,
                [
                    'constraints' => [
                        new UserPassword(
                            [
                                'message' => $this->translator->trans('Field.password.old.incorrect'),
                            ]
                        ),
                    ],
                    'always_empty' => false,
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
