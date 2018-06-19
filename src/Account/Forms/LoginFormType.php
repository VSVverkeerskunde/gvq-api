<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class LoginFormType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'email',
                TextType::class,
                []
            )
            ->add(
                'password',
                PasswordType::class,
                []
            );
    }
}
