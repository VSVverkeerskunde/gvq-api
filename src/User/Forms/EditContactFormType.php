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
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\User\Models\User;
use VSV\GVQ_API\User\ValueObjects\Email;

class EditContactFormType extends AbstractType
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->user = $options['user'];
        $this->translator = $options['translator'];

        $builder
            ->add(
                'email',
                EmailType::class,
                [
                    'data' => $this->user ? $this->user->getEmail()->toNative() : null,
                    'constraints' => [
                        new Regex(
                            [
                                'pattern' => Email::PATTERN,
                                'message' => $this->translator->trans('Field.email.pattern'),
                                'groups' => ['CorrectSyntax'],
                            ]
                        ),
                        new Length(
                            [
                                'max' => 255,
                                'maxMessage' => $this->translator->trans('Field.length.max'),
                                'groups' => ['CorrectSyntax'],
                            ]
                        ),
                        new NotBlank(
                            [
                                'message' => $this->translator->trans('Field.empty'),
                                'groups' => ['CorrectSyntax'],
                            ]
                        ),
                        new UserIsUnique(
                            [
                                'message' => $this->translator->trans('Field.email.in.use'),
                                'userId' => $this->user ? $this->user->getId()->toString() : null,
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'firstName',
                TextType::class,
                [
                    'data' => $this->user ? $this->user->getFirstName()->toNative() : null,
                    'constraints' => $this->createNameConstraint($this->translator),
                ]
            )
            ->add(
                'lastName',
                TextType::class,
                [
                    'data' => $this->user ? $this->user->getLastName()->toNative() : null,
                    'constraints' => $this->createNameConstraint($this->translator),
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
            $user->getLanguage(),
            $user->isActive()
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
