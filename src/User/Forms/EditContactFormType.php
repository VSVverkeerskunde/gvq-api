<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Forms;

use Symfony\Component\Form\AbstractType;
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
use VSV\GVQ_API\User\Models\User;
use VSV\GVQ_API\User\ValueObjects\Email;

class EditContactFormType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
                    ],
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
            );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'user' => null,
                'translator' => null,
            ]
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
