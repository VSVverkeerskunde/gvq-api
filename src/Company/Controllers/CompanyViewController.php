<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Controllers;

use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use VSV\GVQ_API\Common\Controllers\CompanyAwareController;
use VSV\GVQ_API\Common\Controllers\ResponseFactory;
use VSV\GVQ_API\Company\Forms\CompanyFormType;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;
use VSV\GVQ_API\User\Repositories\UserRepository;

class CompanyViewController extends CompanyAwareController
{
    /**
     * @var UuidFactoryInterface
     */
    private $uuidFactory;

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
     * @var CompanyFormType
     */
    private $companyFormType;

    /**
     * @param UuidFactoryInterface $uuidFactory
     * @param UserRepository $userRepository
     * @param CompanyRepository $companyRepository
     * @param SerializerInterface $serializer
     * @param TranslatorInterface $translator
     * @param ResponseFactory $responseFactory
     */
    public function __construct(
        UuidFactoryInterface $uuidFactory,
        UserRepository $userRepository,
        CompanyRepository $companyRepository,
        SerializerInterface $serializer,
        TranslatorInterface $translator,
        ResponseFactory $responseFactory
    ) {
        parent::__construct($userRepository, $companyRepository);

        $this->uuidFactory = $uuidFactory;
        $this->serializer = $serializer;
        $this->translator = $translator;
        $this->responseFactory = $responseFactory;

        $this->companyFormType = new CompanyFormType();
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        $companies = $this->getCompaniesForUser();

        return $this->render(
            'companies/index.html.twig',
            [
                'companies' => $companies ? $companies->toArray() : [],
            ]
        );
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function add(Request $request): Response
    {
        $form = $this->createCompanyForm(null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $company = $this->companyFormType->newCompanyFromData(
                $this->uuidFactory,
                $data,
                $this->getCurrentUser()
            );
            $this->saveCompany($company);

            $this->addFlash(
                'success',
                $this->translator->trans(
                    'Company.add.success',
                    [
                        '%id%' => $company->getId()->toString(),
                    ]
                )
            );

            return $this->redirectToRoute('companies_view_index');
        }

        return $this->render(
            'companies/add.html.twig',
            [
                'form' => $form->createView(),
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
        $companies = $this->getCompaniesForUser();

        $company = $this->getActiveCompany($companies, $id);

        if (!$company) {
            $this->addFlash('warning', $this->translator->trans('Company.edit.not.found', ['%id%' => $id]));

            return $this->redirectToRoute('companies_view_index');
        }

        $form = $this->createCompanyForm($company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $company = $this->companyFormType->updateCompanyFromData(
                $company,
                $form->getData()
            );
            $this->updateCompany($company);

            $this->addFlash(
                'success',
                $this->translator->trans(
                    'Company.edit.success',
                    [
                        '%id%' => $company->getId()->toString(),
                    ]
                )
            );

            return $this->redirectToRoute('companies_view_index');
        }

        return $this->render(
            'companies/add.html.twig',
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
        $companies = $this->getCompaniesForUser();
        $companiesAsCsv = $this->serializer->serialize($companies, 'csv');

        $response = $this->responseFactory->createCsvResponse(
            $companiesAsCsv,
            'companies'
        );

        return $response;
    }

    /**
     * @param null|Company $company
     * @return FormInterface
     */
    private function createCompanyForm(?Company $company): FormInterface
    {
        $formBuilder = $this->createFormBuilder();

        $this->companyFormType->buildForm(
            $formBuilder,
            [
                'company' => $company,
                'translator' => $this->translator,
            ]
        );

        return $formBuilder->getForm();
    }
}
