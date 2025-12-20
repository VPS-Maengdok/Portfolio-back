<?php

namespace App\Controller;

use App\DTO\StatusDTO;
use App\Entity\Status;
use App\Repository\StatusRepository;
use App\Serializer\StatusSerializer;
use App\Service\Shared\ApiResponseService;
use App\Service\Shared\LocaleRequestService;
use App\Service\StatusService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/status', name: 'status')]
class StatusController extends AbstractController
{
    public function __construct(
        private readonly ApiResponseService $apiResponse,
        private readonly StatusRepository $statusRepository,
        private readonly StatusSerializer $statusSerializer,
        private readonly StatusService $statusService
    ) {}

    #[Route('/', name: '_list', methods: ['GET'])]
    public function list(Request $request, LocaleRequestService $localeRepository, StatusRepository $statusRepository): JsonResponse
    {
        $lang = $localeRepository->getLocaleFromRequest($request);
        $data = $this->statusRepository->findAllWithLocale($lang->getId());
        $serializer = $this->statusSerializer->list($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/{id}', name: '_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function details(Request $request, Status $status, LocaleRequestService $localeRequestService, StatusRepository $statusRepository): JsonResponse
    {
        if (!$status) {
            throw new Exception('Status not found.');
        }

        $isFromForm = filter_var($request->query->get('fromForm'), FILTER_VALIDATE_BOOL);
        $lang = $localeRequestService->getLocaleFromRequest($request);
        $data = $isFromForm ?
            $this->statusRepository->findOneById($status->getId()) :
            $this->statusRepository->findOneWithLocale($status->getId(), $lang->getId());
        $serializer = $this->statusSerializer->details($data, $isFromForm, $isFromForm ? null : $lang->getId());

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/', name: '_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload(
            validationGroups: ['create'],
            acceptFormat: 'json'
        )] StatusDTO $dto
    ): JsonResponse {
        $status = $this->statusService->create($dto);
        $serializer = $this->statusSerializer->create($status);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Status successfully created.'], $serializer);
    }

    #[Route('/{id}', name: '_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(
        #[MapRequestPayload(
            validationGroups: ['update'],
            acceptFormat: 'json'
        )] StatusDTO $dto,
        Status $status
    ): JsonResponse {
        if (!$status) {
            throw new Exception('Status not found.');
        }

        $statusService = $this->statusService->update($status, $dto);
        $serializer = $this->statusSerializer->update($statusService);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Status successfully updated.'], $serializer);
    }

    #[Route('/{id}', name: '_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(Status $status): JsonResponse
    {
        if (!$status) {
            throw new Exception('Status not found.');
        }

        $this->statusService->delete($status);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Status successfully deleted.']);
    }
}
