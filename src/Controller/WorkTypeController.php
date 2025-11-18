<?php

namespace App\Controller;

use App\DTO\WorkTypeDTO;
use App\Entity\WorkType;
use App\Repository\WorkTypeRepository;
use App\Serializer\WorkTypeSerializer;
use App\Service\Shared\ApiResponseService;
use App\Service\Shared\LocaleRequestService;
use App\Service\WorkTypeService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/work-type', name: 'work_type')]
class WorkTypeController extends AbstractController
{
    public function __construct(
        private readonly ApiResponseService $apiResponse,
        private readonly WorkTypeSerializer $workTypeSerializer,
        private readonly WorkTypeService $workTypeService,
        private readonly WorkTypeRepository $workTypeRepository
    ) {}

    #[Route('/', name: '_list', methods: ['GET'])]
    public function list(Request $request, LocaleRequestService $localeRepository): JsonResponse
    {
        $lang = $localeRepository->getLocaleFromRequest($request);
        $data = $this->workTypeRepository->findAllWithLocale($lang->getId());
        $serializer = $this->workTypeSerializer->list($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/{id}', name: '_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function details(Request $request, WorkType $workType, LocaleRequestService $localeRequestService): JsonResponse
    {
        if (!$workType) {
            throw new Exception('Work Type not found.');
        }

        $lang = $localeRequestService->getLocaleFromRequest($request);
        $data = $this->workTypeRepository->findOneWithLocale($workType->getId(), $lang->getId());
        $serializer = $this->workTypeSerializer->details($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/', name: '_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload(
            validationGroups: ['create'], 
            acceptFormat: 'json'
        )] WorkTypeDTO $dto
    ): JsonResponse
    {
        $workType = $this->workTypeService->create($dto);
        $serializer = $this->workTypeSerializer->create($workType);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'WorkType successfully created.'], $serializer);
    }

    #[Route('/{id}', name: '_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(
        #[MapRequestPayload(
            validationGroups: ['update'], 
            acceptFormat: 'json'
        )] WorkTypeDTO $dto, 
        WorkType $workType
    ): JsonResponse
    {
        if (!$workType) {
            throw new Exception('WorkType not found.');
        }

        $workTypeService = $this->workTypeService->update($workType->getId(), $dto);
        $serializer = $this->workTypeSerializer->update($workTypeService);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'WorkType successfully updated.'], $serializer);
    }

    #[Route('/{id}', name: '_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(WorkType $workType): JsonResponse
    {
        if (!$workType) {
            throw new Exception('WorkType not found.');
        }

        $this->workTypeService->delete($workType);
        
        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'WorkType successfully deleted.']);
    }
}