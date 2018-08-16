<?php declare(strict_types=1);

namespace VSV\GVQ_API\Dashboard\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\User\Repositories\UserRepository;
use VSV\GVQ_API\User\ValueObjects\Email;

class DashboardViewController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * @param UserRepository $userRepository
     * @param CompanyRepository $companyRepository
     */
    public function __construct(
        UserRepository $userRepository,
        CompanyRepository $companyRepository
    ) {
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
    }

    /**
     * @return Response
     */
    public function dashboard(): Response
    {
        $companies = null;

        if ($this->get('security.authorization_checker')->isGranted(['ROLE_VSV', 'ROLE_ADMIN'])) {
            $companies = $this->companyRepository->getAll();
        } elseif ($this->get('security.authorization_checker')->isGranted(['ROLE_CONTACT'])) {
            $user = $this->userRepository->getByEmail(
                new Email($this->getUser()->getUsername())
            );
            if ($user !== null) {
                $companies = $this->companyRepository->getAllByUser($user);
            }
        }

        return $this->render(
            'dashboard/dashboard.html.twig',
            [
                'companies' => $companies? $companies->toArray() : [],
            ]
        );
    }
}
