<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Controllers;

use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\GroupSequence;
use VSV\GVQ_API\Common\Controllers\ResponseFactory;
use VSV\GVQ_API\Common\ValueObjects\Languages;
use VSV\GVQ_API\User\Forms\EditDataFormType;
use VSV\GVQ_API\User\Forms\UserFormType;
use VSV\GVQ_API\User\Models\User;
use VSV\GVQ_API\User\Repositories\UserRepository;
use VSV\GVQ_API\User\ValueObjects\Email;
use VSV\GVQ_API\User\ValueObjects\Role;
use VSV\GVQ_API\User\ValueObjects\Roles;

class UserViewController extends AbstractController
{
    /**
     * @var UuidFactoryInterface
     */
    private $uuidFactory;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * @var UserFormType
     */
    private $userFormType;

    /**
     * @var EditDataFormType
     */
    private $editDataDFormType;

    /**
     * @param UuidFactoryInterface $uuidFactory
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     * @param TranslatorInterface $translator
     * @param ResponseFactory $responseFactory
     */
    public function __construct(
        UuidFactoryInterface $uuidFactory,
        UserRepository $userRepository,
        SerializerInterface $serializer,
        TranslatorInterface $translator,
        ResponseFactory $responseFactory
    ) {
        $this->uuidFactory = $uuidFactory;
        $this->userRepository = $userRepository;
        $this->serializer = $serializer;
        $this->translator = $translator;
        $this->responseFactory = $responseFactory;

        $this->userFormType = new UserFormType();
        $this->editDataDFormType = new EditDataFormType();
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        $users = $this->userRepository->getAll();

        return $this->render(
            'users/index.html.twig',
            [
                'users' => $users ? $users->toArray() : [],
            ]
        );
    }

    /**
     * @param Request $request
     * @param string $id
     * @return Response
     */
    public function edit(Request $request, string $id): Response
    {
        $user = $this->userRepository->getById(
            $this->uuidFactory->fromString($id)
        );

        if (!$user) {
            $this->addFlash(
                'warning',
                $this->translator->trans(
                    'User.edit.not.found',
                    [
                        '%id%' => $id,
                    ]
                )
            );

            return $this->redirectToRoute('users_view_index');
        }

        $form = $this->createUserForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userFormType->updateUserFromData(
                $user,
                $form->getData()
            );

            $this->userRepository->update($user);

            $this->addFlash(
                'success',
                $this->translator->trans(
                    'User.edit.success',
                    [
                        '%id%' => $id,
                    ]
                )
            );

            return $this->redirectToRoute('users_view_index');
        }

        return $this->render(
            'users/add.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    public function editContact(Request $request, ?string $id): Response
    {
        if ($id === null) {
            $user = $this->userRepository->getByEmail(new Email($this->getUser()->getUsername()));
        } elseif ($this->get('security.authorization_checker')->isGranted('ROLE_CONTACT')) {
            return $this->redirectToRoute('contacts_view_edit', ['id' => null]);
        } else {
            $user = $this->userRepository->getById($this->uuidFactory->fromString($id));
        }

        $form = $this->createEditDataForm($user);

        return $this->render(
            'users/edit_contact.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @return Response
     */
    public function export(): Response
    {
        $users = $this->userRepository->getAll();
        $usersAsCsv = $this->serializer->serialize($users, 'csv');

        $response = $this->responseFactory->createCsvResponse(
            $usersAsCsv,
            'users'
        );

        return $response;
    }

    /**
     * @param null|User $user
     * @return FormInterface
     */
    private function createUserForm(?User $user): FormInterface
    {
        $formBuilder = $this->createFormBuilder(
            null,
            [
                'validation_groups' => new GroupSequence(
                    [
                        'CorrectSyntax',
                        'Default',
                    ]
                ),
            ]
        );

        $this->userFormType->buildForm(
            $formBuilder,
            [
                'roles' => new Roles(
                    new Role('contact'),
                    new Role('vsv'),
                    new Role('admin')
                ),
                'languages' => new Languages(),
                'user' => $user,
                'translator' => $this->translator,
            ]
        );

        return $formBuilder->getForm();
    }

    /**
     * @param null|User $user
     * @return FormInterface
     */
    private function createEditDataForm(?User $user): FormInterface
    {
        $formBuilder = $this->createFormBuilder();

        $this->editDataDFormType->buildForm(
            $formBuilder,
            [
                'user' => $user,
                'translator' => $this->translator,
            ]
        );

        return $formBuilder->getForm();
    }
}
