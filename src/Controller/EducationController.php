<?php

namespace App\Controller;

use App\DTO\EducationDTO;
use App\Entity\Education;
use App\Repository\EducationRepository;
use App\Serializer\EducationSerializer;
use App\Service\EducationService;
use App\Service\Shared\ApiResponseService;
use App\Service\Shared\LocaleRequestService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/education', name: 'education')]
class EducationController extends AbstractController
{
    public function __construct(
        private readonly ApiResponseService $apiResponse,
        private readonly EducationSerializer $educationSerializer,
        private readonly EducationRepository $educationRepository,
        private readonly EducationService $educationService
    ) {}

    #[Route('/', name: '_list', methods: ['GET'])]
    public function list(Request $request, LocaleRequestService $localeRepository): JsonResponse
    {
        $lang = $localeRepository->getLocaleFromRequest($request);
        $data = $this->educationRepository->findAllWithLocale($lang->getId());
        $serializer = $this->educationSerializer->list($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/{id}', name: '_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function details(Request $request, Education $education, LocaleRequestService $localeRequestService): JsonResponse
    {
        if (!$education) {
            throw new Exception('Education not found.');
        }

        $lang = $localeRequestService->getLocaleFromRequest($request);
        $data = $this->educationRepository->findOneWithLocale($education->getId(), $lang->getId());
        $serializer = $this->educationSerializer->details($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/', name: '_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload(
            validationGroups: ['create'], 
            acceptFormat: 'json'
        )] EducationDTO $dto
    ): JsonResponse
    {
        $education = $this->educationService->create($dto);
        $serializer = $this->educationSerializer->create($education);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Education successfully created.'], $serializer);
    }

    #[Route('/{id}', name: '_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(
        #[MapRequestPayload(
            validationGroups: ['update'], 
            acceptFormat: 'json'
        )] EducationDTO $dto, 
        Education $education
    ): JsonResponse
    {
        if (!$education) {
            throw new Exception('Education not found.');
        }

        $educationService = $this->educationService->update($education, $dto);
        $serializer = $this->educationSerializer->update($educationService);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Education successfully updated.'], $serializer);
    }

    #[Route('/{id}', name: '_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(Education $education): JsonResponse
    {
        if (!$education) {
            throw new Exception('Education not found.');
        }

        $this->educationService->delete($education);
        
        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Education successfully deleted.']);
    }
}