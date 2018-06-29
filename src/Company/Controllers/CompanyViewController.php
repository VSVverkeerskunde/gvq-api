<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Controllers;

use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use VSV\GVQ_API\Common\Controllers\ResponseFactory;
use VSV\GVQ_API\Company\Forms\CompanyFormType;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Repositories\CompanyRepository;

class CompanyViewController extends AbstractController
{
    /**
     * @var UuidFactoryInterface
     */
    private $uuidFactory;

    /**
     * @var CompanyRepository
     */
    private $companyRepository;

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
     * @param CompanyRepository $companyRepository
     * @param SerializerInterface $serializer
     * @param TranslatorInterface $translator
     * @param ResponseFactory $responseFactory
     */
    public function __construct(
        UuidFactoryInterface $uuidFactory,
        CompanyRepository $companyRepository,
        SerializerInterface $serializer,
        TranslatorInterface $translator,
        ResponseFactory $responseFactory
    ) {
        $this->uuidFactory = $uuidFactory;
        $this->companyRepository = $companyRepository;
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
        $companies = $this->companyRepository->getAll();

        return $this->render(
            'companies/index.html.twig',
            [
                'companies' => $companies ? $companies->toArray() : [],
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
        $company = $this->companyRepository->getById(
            $this->uuidFactory->fromString($id)
        );

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
            $this->companyRepository->update($company);

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
        $companies = $this->companyRepository->getAll();
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
