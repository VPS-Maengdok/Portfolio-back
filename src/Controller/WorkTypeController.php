<?php

namespace App\Controller;

use App\Entity\WorkType;
use App\Repository\WorkTypeRepository;
use App\Serializer\WorkTypeSerializer;
use App\Service\Shared\ApiResponseService;
use App\Service\Shared\LocaleRequestService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/work-type', name: 'work_type')]
class WorkTypeController extends AbstractController
{
    public function __construct(
        private readonly ApiResponseService $apiResponse,
        private readonly WorkTypeSerializer $workTypeSerializer
    ) {}

    #[Route('/', name: '_list', methods: ['GET'])]
    public function list(Request $request, LocaleRequestService $localeRepository, WorkTypeRepository $workTypeRepository): JsonResponse
    {
        $lang = $localeRepository->getLocaleFromRequest($request);
        $data = $workTypeRepository->findAllWithLocale($lang->getId());
        $serializer = $this->workTypeSerializer->list($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/{id}', name: '_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function details(Request $request, WorkType $workType, LocaleRequestService $localeRequestService, WorkTypeRepository $workTypeRepository): JsonResponse
    {
        if (!$workType) {
            throw new Exception('Work Type not found.');
        }

        $lang = $localeRequestService->getLocaleFromRequest($request);
        $data = $workTypeRepository->findOneWithLocale($workType->getId(), $lang->getId());
        $serializer = $this->workTypeSerializer->details($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }
}