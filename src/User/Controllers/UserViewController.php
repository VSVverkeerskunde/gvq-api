<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Controllers;

use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use VSV\GVQ_API\Common\ValueObjects\Languages;
use VSV\GVQ_API\User\Forms\UserFormType;
use VSV\GVQ_API\User\Models\User;
use VSV\GVQ_API\User\Repositories\UserRepository;
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
     * @var UserFormType
     */
    private $userFormType;

    /**
     * @param UuidFactoryInterface $uuidFactory
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     * @param TranslatorInterface $translator
     */
    public function __construct(
        UuidFactoryInterface $uuidFactory,
        UserRepository $userRepository,
        SerializerInterface $serializer,
        TranslatorInterface $translator
    ) {
        $this->uuidFactory = $uuidFactory;
        $this->userRepository = $userRepository;
        $this->serializer = $serializer;
        $this->translator = $translator;

        $this->userFormType = new UserFormType();
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
                'users' => $users ? $users->toArray(): [],
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
            $this->addFlash('warning', 'Geen gebruiker gevonden met id '.$id.' om aan te passen.');
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

            $this->addFlash('success', 'Gebruiker '.$user->getEmail()->toNative().' is aangepast.');
            return $this->redirectToRoute('users_view_index');
        }

        return $this->render(
            'users/add.html.twig',
            [
                'form' => $form->createView()
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

        $usersAsCsv = $this->createCsvData($usersAsCsv);
        $response = $this->createCsvResponse($usersAsCsv);

        return $response;
    }

    /**
     * @param null|User $user
     * @return FormInterface
     */
    private function createUserForm(?User $user): FormInterface
    {
        $formBuilder = $this->createFormBuilder();

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
     * @param string $data
     * @return string
     */
    private function createCsvData(string $data): string
    {
        /**
         * @see: https://github.com/thephpleague/csv/blob/507815707cbdbebaf076873bff04cd6ad65fe0fe/docs/9.0/connections/bom.md
         */
        $csvData = chr(0xFF).chr(0xFE);
        $csvData .= mb_convert_encoding('sep=,'.PHP_EOL.$data, 'UTF-16LE', 'UTF-8');
        return $csvData;
    }

    /**
     * @param string $csvData
     * @return Response
     */
    private function createCsvResponse(string $csvData): Response
    {
        $response = new Response($csvData);

        $response->headers->set('Content-Encoding', 'UTF-8');
        $response->headers->set('Content-Type', 'application/csv; charset=UTF-8');
        $response->headers->set('Content-Transfer-Encoding', 'binary');

        $now = new \DateTime();
        $fileName = 'users_'.$now->format(\DateTime::ATOM).'.csv';
        $response->headers->set(
            'Content-Disposition',
            'attachment; filename="'.$fileName.'"'
        );

        return $response;
    }
}
