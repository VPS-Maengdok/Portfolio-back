<?php

namespace App\Controller;

use App\DTO\CompanyDTO;
use App\Entity\Company;
use App\Repository\CompanyRepository;
use App\Serializer\CompanySerializer;
use App\Service\Shared\ApiResponseService;
use App\Service\CompanyService;
use App\Service\Shared\LocaleRequestService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/company', name: 'company')]
class CompanyController extends AbstractController
{
    public function __construct(
        private readonly ApiResponseService $apiResponse,
        private readonly CompanyRepository $companyRepository,
        private readonly CompanyService $companyService
    ) {}

    #[Route('/', name:'_list', methods: ['GET'])]
    public function list(Request $request, LocaleRequestService $localeRequestService): JsonResponse
    {
        $lang = $localeRequestService->getLocale($request);
        $data = $this->companyRepository->findAllWithLocale($lang->getId());
        $serializer = CompanySerializer::list($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/{id}', name: '_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function details(Request $request, Company $company, LocaleRequestService $localeRequestService): JsonResponse
    {
        if (!$company) {
            throw new Exception('Company not found.');
        }

        $lang = $localeRequestService->getLocale($request);
        $data = $this->companyRepository->findOneWithLocale($company->getId(), $lang->getId());
        $serializer = CompanySerializer::details($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/', name: '_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload(
            validationGroups: ['create'], 
            acceptFormat: 'json'
        )] CompanyDTO $dto
    ): JsonResponse
    {
        $company = $this->companyService->create($dto);
        $serializer = CompanySerializer::create($company);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Company successfully created.'], $serializer);
    }

    #[Route('/{id}', name: '_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(
        #[MapRequestPayload(
            validationGroups: ['update'], 
            acceptFormat: 'json'
        )] CompanyDTO $dto, 
        Company $company
    ): JsonResponse
    {
        if (!$company) {
            throw new Exception('Company not found.');
        }

        $companyService = $this->companyService->update($company->getId(), $dto);
        $serializer = CompanySerializer::update($company);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Company successfully updated.'], $serializer);
    }

    #[Route('/{id}', name: '_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(Company $company): JsonResponse
    {
        if (!$company) {
            throw new Exception('Company not found.');
        }

        $this->companyService->delete($company);
        
        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Company successfully deleted.']);
    }
}
